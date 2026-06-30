<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // Nullable if system-wide
            $table->string('from_name')->nullable();
            $table->string('from_email');
            
            // SMTP Settings
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // Encrypted
            $table->string('smtp_encryption')->nullable();

            // IMAP Settings
            $table->string('imap_host')->nullable();
            $table->integer('imap_port')->nullable();
            $table->string('imap_username')->nullable();
            $table->text('imap_password')->nullable(); // Encrypted
            $table->string('imap_encryption')->nullable();

            // POP Settings (Optional)
            $table->string('pop_host')->nullable();
            $table->integer('pop_port')->nullable();
            $table->string('pop_username')->nullable();
            $table->text('pop_password')->nullable(); // Encrypted
            $table->string('pop_encryption')->nullable();

            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_accounts');
    }
};
