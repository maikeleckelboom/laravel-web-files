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
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('chunk_size');
            $table->unsignedInteger('received_chunks')->default(0);
            $table->enum('status', UploadStatus::toArray())->default(UploadStatus::INITIATED);
            $table->string('disk')->default('uploaded_files');
            $table->string('chunks_disk')->default('chunks');
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
