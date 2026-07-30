<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('type', 50)->default('text')->after('content');
            $table->string('media_url', 2048)->nullable()->after('type');
            $table->unsignedBigInteger('reply_to_id')->nullable()->after('media_url');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['type', 'media_url', 'reply_to_id']);
        });
    }
};
