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

    public function test_download_android_returns_not_found_without_release(): void
    {
        $response = $this->get(route('android-app.download'));

        $response->assertNotFound();
    }

    public function test_download_android_serves_registered_apk(): void
    {
        Storage::fake('public');
        $diskPath = 'mobile-app/QuantumRealm.apk';
        Storage::disk('public')->put($diskPath, 'fake-binary-apk');

        MobileAppRelease::create([
            'version_name' => '1.0',
            'version_code' => 1,
            'disk_path' => $diskPath,
            'original_filename' => 'QuantumRealm.apk',
            'size_bytes' => 15,
            'is_current' => true,
        ]);

        $response = $this->get(route('android-app.download'));

        $response->assertOk();
        $response->assertDownload('QuantumRealm.apk');
    }

    public function test_public_meta_marks_apk_available_when_file_exists(): void
    {
        Storage::fake('public');
        $diskPath = 'mobile-app/QuantumRealm.apk';
        Storage::disk('public')->put($diskPath, 'fake-binary-apk');

        MobileAppRelease::create([
            'version_name' => '1.0',
            'version_code' => 1,
            'disk_path' => $diskPath,
            'original_filename' => 'QuantumRealm.apk',
            'size_bytes' => 15,
            'is_current' => true,
        ]);

        $meta = app(MobileAppReleaseService::class)->publicMeta();

        $this->assertTrue($meta['available']);
        $this->assertSame('/download/android', $meta['download_url']);
    }

    public function test_ensure_bundled_registers_apk_for_download(): void
    {
        Storage::fake('public');
        config([
            'mobile-app.bundled_disk_path' => 'mobile-app/QuantumRealm.apk',
            'mobile-app.bundled_version_name' => '1.0',
            'mobile-app.bundled_version_code' => 1,
        ]);

        Storage::disk('public')->put('mobile-app/QuantumRealm.apk', str_repeat('x', 64));

        $this->artisan('mobile-app:ensure-bundled')->assertSuccessful();

        $meta = app(MobileAppReleaseService::class)->publicMeta();
        $this->assertTrue($meta['available']);

        $this->get(route('android-app.download'))->assertOk();
    }
}
