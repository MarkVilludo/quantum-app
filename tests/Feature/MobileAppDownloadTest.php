<?php

namespace Tests\Feature;

use App\Models\MobileAppRelease;
use App\Services\MobileAppReleaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MobileAppDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => database_path('database.sqlite'),
        ]);
    }

    private function fakeApkPayload(string $suffix = 'payload'): string
    {
        return "PK\x03\x04".$suffix;
    }

    private function registerRelease(string $diskPath, int $sizeBytes): void
    {
        MobileAppRelease::create([
            'version_name' => '1.0',
            'version_code' => 1,
            'disk_path' => $diskPath,
            'original_filename' => 'QuantumRealm.apk',
            'size_bytes' => $sizeBytes,
            'is_current' => true,
        ]);
    }

    public function test_download_android_returns_not_found_without_release(): void
    {
        $response = $this->get(route('android-app.download'));

        $response->assertNotFound();
    }

    public function test_download_android_redirects_to_static_apk_when_synced(): void
    {
        Storage::fake('public');
        $diskPath = 'mobile-app/QuantumRealm.apk';
        $payload = $this->fakeApkPayload();
        Storage::disk('public')->put($diskPath, $payload);
        $this->registerRelease($diskPath, strlen($payload));

        app(MobileAppReleaseService::class)->syncStaticDownloadFromDiskPath($diskPath);

        $this->get(route('android-app.download'))
            ->assertRedirect('/downloads/QuantumRealm.apk');
    }

    public function test_sync_static_download_writes_zip_magic_to_public_path(): void
    {
        Storage::fake('public');
        $diskPath = 'mobile-app/QuantumRealm.apk';
        $payload = $this->fakeApkPayload('binary-apk');
        Storage::disk('public')->put($diskPath, $payload);
        $this->registerRelease($diskPath, strlen($payload));

        $releases = app(MobileAppReleaseService::class);
        $this->assertTrue($releases->syncStaticDownloadFromDiskPath($diskPath));

        $staticPath = $releases->staticDownloadPublicPath();
        $this->assertFileExists($staticPath);
        $this->assertSame("PK\x03\x04", file_get_contents($staticPath, false, null, 0, 4));
        $this->assertSame(strlen($payload), filesize($staticPath));
        $this->assertTrue($releases->staticDownloadIsValid());
    }

    public function test_public_meta_marks_apk_available_when_file_exists(): void
    {
        Storage::fake('public');
        $diskPath = 'mobile-app/QuantumRealm.apk';
        $payload = $this->fakeApkPayload();
        Storage::disk('public')->put($diskPath, $payload);
        $this->registerRelease($diskPath, strlen($payload));

        app(MobileAppReleaseService::class)->syncStaticDownloadFromDiskPath($diskPath);

        $meta = app(MobileAppReleaseService::class)->publicMeta();

        $this->assertTrue($meta['available']);
        $this->assertSame('/downloads/QuantumRealm.apk', $meta['download_url']);
    }

    public function test_ensure_bundled_registers_apk_for_download(): void
    {
        Storage::fake('public');
        config([
            'mobile-app.bundled_disk_path' => 'mobile-app/QuantumRealm.apk',
            'mobile-app.bundled_version_name' => '1.0',
            'mobile-app.bundled_version_code' => 1,
        ]);

        Storage::disk('public')->put('mobile-app/QuantumRealm.apk', $this->fakeApkPayload(str_repeat('x', 60)));

        $this->artisan('mobile-app:ensure-bundled')->assertSuccessful();

        $meta = app(MobileAppReleaseService::class)->publicMeta();
        $this->assertTrue($meta['available']);

        $releases = app(MobileAppReleaseService::class);
        $this->assertTrue($releases->staticDownloadIsValid());
    }
}
