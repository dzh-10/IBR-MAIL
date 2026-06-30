<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // if linked to an internal user
            $table->string('name');
            $table->string('email')->index();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_group_pivot', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('contact_groups')->cascadeOnDelete();
            $table->primary(['contact_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_group_pivot');
        Schema::dropIfExists('contact_groups');
        Schema::dropIfExists('contacts');
    }
};
