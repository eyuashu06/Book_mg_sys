<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('book_file')->nullable()->after('cover_image');
            $table->string('file_type')->nullable()->after('book_file');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
            $table->string('reading_status')->default('unread')->after('file_size');
            $table->unsignedSmallInteger('reading_progress')->default(0)->after('reading_status');
            $table->json('metadata')->nullable()->after('reading_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['book_file', 'file_type', 'file_size', 'reading_status', 'reading_progress', 'metadata']);
        });
    }
};
