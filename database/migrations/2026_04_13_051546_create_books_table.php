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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('book_name');
            $table->text('description');
            $table->enum('language',['arabic','english','chinese','spanish','hindi',
            'portuguese','russian','japanese','punjabi','german','korean',
            'french','turkish','urdu','italian']);
            $table->integer('number_of_pages');
            $table->decimal('selling_price',10,2);
            $table->decimal('rental_price',10,2);
            $table->integer('number_of_reads')->default(0);
            $table->integer('rating_sum')->default(0);
            $table->integer('rating_count')->default(0);
            $table->decimal('rating',3,2)->default(0);
            $table->string('book_file');
            $table->string('cover_image');
            $table->json('book_images');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
