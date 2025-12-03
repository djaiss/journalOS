<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emails_sent', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('uuid')->nullable();
            $table->text('email_type');
            $table->text('email_address');
            $table->text('subject')->nullable();
            $table->text('body')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            $table->datetime('bounced_at')->nullable();
            $table->timestamps();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails_sent');
    }
};
