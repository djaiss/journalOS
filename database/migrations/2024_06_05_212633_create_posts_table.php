<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('label')->nullable();
            $table->string('label_translation_key')->nullable();
            $table->integer('position');
            $table->boolean('can_be_deleted')->default(true);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('post_template_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_template_id');
            $table->string('label')->nullable();
            $table->string('label_translation_key')->nullable();
            $table->integer('position');
            $table->boolean('can_be_deleted')->default(true);
            $table->timestamps();
            $table->foreign('post_template_id')->references('id')->on('post_templates')->onDelete('cascade');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id');
            $table->string('title')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('number_of_words')->default(0);
            $table->integer('reading_time_in_seconds')->default(0);
            $table->datetime('written_at');
            $table->timestamps();
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
        });

        Schema::create('post_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->integer('position');
            $table->string('label');
            $table->text('content')->nullable();
            $table->timestamps();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_sections');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_template_sections');
        Schema::dropIfExists('post_templates');
    }
};
