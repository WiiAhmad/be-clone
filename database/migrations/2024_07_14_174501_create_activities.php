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
        Schema::create('activities', function (Blueprint $table) {
            $table->id(); // Primary Key with auto-increment
            $table->string('title'); // Title of the activity
            $table->text('desc')->nullable(); // Description of the activity, nullable
            $table->string('image')->nullable(); // Path to the image, nullable
            $table->timestamp('date')->useCurrent(); // Date and time of the activity
            $table->timestamps(); // Created_at and Updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
