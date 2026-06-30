<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_account_id')->constrained()->cascadeOnDelete();
            $table->string('message_id')->unique()->nullable();
            $table->string('thread_id')->nullable();
            $table->text('from'); // JSON array or string
            $table->text('to'); // JSON array
            $table->text('cc')->nullable(); // JSON array
            $table->text('bcc')->nullable(); // JSON array
            $table->string('subject')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->enum('direction', ['inbound', 'outbound'])->default('inbound');
            $table->string('folder')->default('INBOX'); // INBOX, Sent, Trash, Spam, Drafts
            $table->boolean('is_read')->default(false);
            $table->boolean('starred')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_messages');
    }
};
