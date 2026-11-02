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

    protected $description = 'Register the git-tracked APK in the database so the homepage download button appears';

    public function handle(MobileAppReleaseService $releases): int
    {
        $diskPath = (string) config('mobile-app.bundled_disk_path', 'mobile-app/QuantumRealm.apk');
        $disk = Storage::disk('public');

        if (! $disk->exists($diskPath)) {
            $this->error('Bundled APK missing at storage/app/public/'.$diskPath);

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

        $this->info('Registered bundled APK for download ('.$versionName.').');

        return self::SUCCESS;
    }
}
