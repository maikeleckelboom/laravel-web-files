<?php

use App\Models\User;
use App\UploadStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('path')->nullable();
            $table->string('disk')->default('uploads');
            $table->string('chunks_disk')->default('chunks');
            $table->unsignedBigInteger('size');
            $table->unsignedBigInteger('chunk_size');
            $table->unsignedInteger('received_chunks')->default(0);
            $table->enum('status', UploadStatus::toArray())->default(UploadStatus::INITIATED);
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
