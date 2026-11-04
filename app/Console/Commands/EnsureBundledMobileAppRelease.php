<?php

namespace App\Console\Commands;

use App\Models\MobileAppRelease;
use App\Services\MobileAppReleaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnsureBundledMobileAppRelease extends Command
{
    protected $signature = 'mobile-app:ensure-bundled
                            {--force : Register bundled APK even when another current release exists}';

    protected $description = 'Register the bundled APK in the database so the homepage download button appears';

    public function handle(MobileAppReleaseService $releases): int
    {
        $diskPath = (string) config('mobile-app.bundled_disk_path', 'mobile-app/QuantumRealm.apk');
        $disk = Storage::disk('public');

        if (! $this->ensureApkOnPublicDisk($disk, $diskPath)) {
            $this->error('APK not found. Expected storage/app/public/'.$diskPath);
            $source = config('mobile-app.source_apk');
            if (is_string($source) && $source !== '') {
                $this->line('MOBILE_APP_SOURCE_APK='.$source);
            }

            return self::FAILURE;
        }

        $absolutePath = $disk->path($diskPath);
        $current = $releases->current();

        if (! $this->option('force') && $current !== null && $disk->exists($current->disk_path)) {
            $this->info('Current mobile app release is already available.');

            return self::SUCCESS;
        }

        $versionName = (string) config('mobile-app.bundled_version_name', '1.0');
        $versionCode = (int) config('mobile-app.bundled_version_code', 1);

        DB::transaction(function () use ($diskPath, $absolutePath, $versionName, $versionCode) {
            MobileAppRelease::query()->where('is_current', true)->update(['is_current' => false]);

            MobileAppRelease::create([
                'version_name' => $versionName,
                'version_code' => $versionCode,
                'disk_path' => $diskPath,
                'original_filename' => basename($diskPath),
                'size_bytes' => (int) filesize($absolutePath),
                'uploaded_by' => null,
                'is_current' => true,
            ]);
        });

        $releases->syncStaticDownloadFromDiskPath($diskPath);

        $this->info('Registered APK at '.$diskPath.' for download ('.$versionName.').');
        $this->line('Static URL: '.$releases->staticDownloadUrl());

        return self::SUCCESS;
    }

    /**
     * Ensure the bundled relative path exists on the public disk, copying from MOBILE_APP_SOURCE_APK if needed.
     */
    private function ensureApkOnPublicDisk($disk, string $diskPath): bool
    {
        if ($disk->exists($diskPath)) {
            return true;
        }

        $source = config('mobile-app.source_apk');
        if (! is_string($source) || $source === '' || ! is_readable($source)) {
            return false;
        }

        $directory = dirname($diskPath);
        if ($directory !== '.' && $directory !== '') {
            $disk->makeDirectory($directory);
        }

        $disk->put($diskPath, file_get_contents($source));

        return $disk->exists($diskPath);
    }
}
