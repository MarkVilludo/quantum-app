<?php

namespace App\Console\Commands;

use App\Services\MobileAppReleaseService;
use Illuminate\Console\Command;

class ImportMobileAppApk extends Command
{
    protected $signature = 'mobile-app:import-apk
                            {path? : Path to app-debug.apk}
                            {--version-name=1.0 : Version name label}
                            {--version-code=1 : Version code integer}';

    protected $description = 'Import a built APK from disk into public storage for web download';

    public function handle(MobileAppReleaseService $releases): int
    {
        $path = $this->argument('path') ?? config('mobile-app.source_apk');

        if (! is_string($path) || $path === '') {
            $this->error('Provide a path argument or set MOBILE_APP_SOURCE_APK in .env');

            return self::FAILURE;
        }

        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        try {
            $release = $releases->importFromPath(
                $path,
                (string) $this->option('version-name'),
                (int) $this->option('version-code'),
                null
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Imported {$release->original_filename} as v{$release->version_name} ({$release->size_bytes} bytes).");
        $this->line('Download URL: '.route('android-app.download'));

        return self::SUCCESS;
    }
}
