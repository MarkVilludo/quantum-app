<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\MobileAppReleaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MobileAppReleaseController extends Controller
{
    public function __construct(
        private MobileAppReleaseService $releases
    ) {}

    public function edit(Request $request)
    {
        $this->ensureAdmin($request);

        $current = $this->releases->current();

        return Inertia::render('Admin/MobileAppRelease', [
            'release' => $current ? [
                'version_name' => $current->version_name,
                'version_code' => $current->version_code,
                'original_filename' => $current->original_filename,
                'size_human' => $this->releases->publicMeta()['size_human'],
                'updated_at' => $current->updated_at?->toIso8601String(),
                'download_url' => $this->releases->publicMeta()['download_url'] ?? $this->releases->staticDownloadUrl(),
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'version_name' => ['required', 'string', 'max:64'],
            'version_code' => ['nullable', 'integer', 'min:1', 'max:2147483647'],
            'apk' => ['required', 'file', 'max:512000'],
        ]);

        $upload = $request->file('apk');
        if (strtolower($upload->getClientOriginalExtension()) !== 'apk') {
            return back()->withErrors(['apk' => 'The file must be an .apk package.']);
        }

        $this->releases->storeUpload(
            $request->file('apk'),
            $validated['version_name'],
            $validated['version_code'] ?? null,
            $request->user()
        );

        return redirect()->route('admin.mobile-app.edit')->with('success', 'Android app uploaded. The homepage download link is updated.');
    }

    public function download(): RedirectResponse|BinaryFileResponse
    {
        $release = $this->releases->current();

        abort_unless($release !== null, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($release->disk_path), 404);

        if (! $this->releases->staticDownloadIsValid()) {
            $this->releases->syncStaticDownloadFromDiskPath($release->disk_path);
        }

        if ($this->releases->staticDownloadIsValid()) {
            return redirect($this->releases->staticDownloadUrl());
        }

        $downloadName = (string) config('mobile-app.download_filename', 'QuantumRealm.apk');

        return response()->file($disk->path($release->disk_path), [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403);
    }
}
