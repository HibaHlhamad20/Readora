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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('slug',['literature','horror','history','poetry','science','fantasy','adventure',
            'self_development','business','marketing','finance','programming','data_science',
            'physics','chemistry','biology','astronomy','education','art','cooking','health','children',
            'psychology','personal_growth','philosophy','religion','politics','economics','sociology',
            'memoirs','short_stories','romance','crime','science_fiction','translated_books',
            'geography','technology','cybersecurity','artificial_intelligence','mathematics',
            'statistics','academic_books','comics','sports','parenting','family_relationships']);
            $table->text('description')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
