<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_app_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version_name');
            $table->unsignedInteger('version_code')->nullable();
            $table->string('disk_path');
            $table->string('original_filename');
            $table->unsignedBigInteger('size_bytes');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_app_releases');
    }
};
