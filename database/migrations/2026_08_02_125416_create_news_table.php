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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->string('type', 20)->default('news'); //announcement
            $table->string('source_type', 20)->default('local'); //external
            $table->text('external_id')->nullable();
            $table->text('external_url')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_breaking')->default(false);
            $table->dateTime('breaking_until')->nullable();
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();;
            $table->timestamps();
            $table->index('status');

            $table->index('published_at');

            $table->index('is_breaking');

            $table->index('source_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
