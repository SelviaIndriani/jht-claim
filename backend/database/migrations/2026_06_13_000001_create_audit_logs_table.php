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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Action & Model tracking
            $table->string('action');                    // created, updated, deleted, verified, etc
            $table->string('model_type');                // Full class name
            $table->unsignedBigInteger('model_id');      // ID of affected model
            $table->index(['model_type', 'model_id']);

            // User tracking
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_email')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
            $table->index('user_email');

            // Change tracking
            $table->json('before_values')->nullable();   // Original values
            $table->json('after_values')->nullable();    // New values
            $table->json('changed_fields')->nullable();  // Which fields changed
            $table->text('reason')->nullable();          // Why the change was made

            // Request context
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id')->nullable();
            $table->index('request_id');

            // Status tracking
            $table->enum('status', ['success', 'failed', 'pending'])->default('success');
            $table->text('error_message')->nullable();

            // Timestamps
            $table->timestamps();
            $table->index('created_at');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
