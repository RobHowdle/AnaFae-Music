<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();

            $table->string('anchor');
            $table->string('nav_label');
            $table->string('heading');
            $table->longText('body')->nullable();

            $table->string('background_image_path')->nullable();
            $table->string('background_position')->default('center');

            $table->unsignedInteger('sort_order')->default(0);

            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();

            $table->timestamps();

            $table->unique(['page_id', 'anchor']);
            $table->index(['page_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
