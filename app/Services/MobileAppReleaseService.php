<?php

namespace App\Services;

use App\Models\MobileAppRelease;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MobileAppReleaseService
{
    public function current(): ?MobileAppRelease
    {
        return MobileAppRelease::query()
            ->where('is_current', true)
            ->latest('id')
            ->first();
    }

    /**
     * @return array{available: bool, version_name: ?string, version_code: ?int, size_human: ?string, download_url: ?string, updated_at: ?string}
     */
    public function publicMeta(): array
    {
        $downloadUrl = $this->staticDownloadUrl();
        $release = $this->current();

        if ($release === null || ! Storage::disk('public')->exists($release->disk_path)) {
            return [
                'available' => false,
                'version_name' => null,
                'version_code' => null,
                'size_human' => null,
                'download_url' => $downloadUrl,
                'updated_at' => null,
            ];
        }

        return [
            'available' => true,
            'version_name' => $release->version_name,
            'version_code' => $release->version_code,
            'size_human' => $this->formatBytes($release->size_bytes),
            'download_url' => $this->staticDownloadIsValid() ? $downloadUrl : '/download/android',
            'updated_at' => $release->updated_at?->toIso8601String(),
        ];
    }

    public function staticDownloadUrl(): string
    {
        $filename = (string) config('mobile-app.download_filename', 'QuantumRealm.apk');
        $directory = trim((string) config('mobile-app.static_download_subdirectory', 'downloads'), '/');

        return '/'.$directory.'/'.$filename;
    }

    public function staticDownloadPublicPath(): string
    {
        $filename = (string) config('mobile-app.download_filename', 'QuantumRealm.apk');
        $directory = trim((string) config('mobile-app.static_download_subdirectory', 'downloads'), '/');

        return public_path($directory.DIRECTORY_SEPARATOR.$filename);
    }

    public function staticDownloadIsValid(): bool
    {
        $path = $this->staticDownloadPublicPath();

        if (! is_file($path)) {
            return false;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $magic = fread($handle, 4);
        fclose($handle);

        return $magic === "PK\x03\x04";
    }

    public function syncStaticDownloadFromDiskPath(string $diskPath): bool
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($diskPath)) {
            return false;
        }

        $dest = $this->staticDownloadPublicPath();
        $directory = dirname($dest);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return false;
        }

        $source = $disk->path($diskPath);

        return copy($source, $dest);
    }

    public function storeUpload(UploadedFile $file, string $versionName, ?int $versionCode, ?User $uploader): MobileAppRelease
    {
        return DB::transaction(function () use ($file, $versionName, $versionCode, $uploader) {
            $directory = config('mobile-app.storage_directory', 'mobile-app');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'apk');
            if ($extension !== 'apk') {
                $extension = 'apk';
            }

            $filename = 'quantum-realm-'.Str::slug($versionName).'-'.now()->format('YmdHis').'.'.$extension;
            $path = $file->storeAs($directory, $filename, 'public');

            MobileAppRelease::query()->where('is_current', true)->update(['is_current' => false]);

            $this->deleteOrphanedApkFiles($directory, $path);

            $release = MobileAppRelease::create([
                'version_name' => $versionName,
                'version_code' => $versionCode,
                'disk_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'size_bytes' => (int) $file->getSize(),
                'uploaded_by' => $uploader?->id,
                'is_current' => true,
            ]);

            $this->syncStaticDownloadFromDiskPath($path);

            return $release;
        });
    }

    public function importFromPath(string $absolutePath, string $versionName, ?int $versionCode, ?User $uploader): MobileAppRelease
    {
        if (! is_readable($absolutePath)) {
            throw new \InvalidArgumentException("APK not readable: {$absolutePath}");
        }

        $directory = config('mobile-app.storage_directory', 'mobile-app');
        $filename = 'quantum-realm-'.Str::slug($versionName).'-'.now()->format('YmdHis').'.apk';
        $dest = $directory.'/'.$filename;

        Storage::disk('public')->put($dest, file_get_contents($absolutePath));

        return DB::transaction(function () use ($dest, $absolutePath, $versionName, $versionCode, $uploader) {
            MobileAppRelease::query()->where('is_current', true)->update(['is_current' => false]);

            $this->deleteOrphanedApkFiles(config('mobile-app.storage_directory', 'mobile-app'), $dest);

            $release = MobileAppRelease::create([
                'version_name' => $versionName,
                'version_code' => $versionCode,
                'disk_path' => $dest,
                'original_filename' => basename($absolutePath),
                'size_bytes' => (int) filesize($absolutePath),
                'uploaded_by' => $uploader?->id,
                'is_current' => true,
            ]);

            $this->syncStaticDownloadFromDiskPath($dest);

            return $release;
        });
    }

    private function deleteOrphanedApkFiles(string $directory, string $keepPath): void
    {
        $disk = Storage::disk('public');
        foreach ($disk->files($directory) as $file) {
            if ($file !== $keepPath && str_ends_with(strtolower($file), '.apk')) {
                $disk->delete($file);
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0).' KB';
        }

        return $bytes.' B';
    }
}
