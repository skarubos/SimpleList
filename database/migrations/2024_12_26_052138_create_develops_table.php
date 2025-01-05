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
        Schema::create('develops', function (Blueprint $table) {
            $table->id();
            $table->text('text1')->nullable();
            $table->text('text2')->nullable();
            $table->text('text3')->nullable();
            $table->integer('int1')->nullable();
            $table->integer('int2')->nullable();
            $table->integer('int3')->nullable();
            $table->integer('int4')->nullable();
            $table->integer('int5')->nullable();
            $table->date('date')->nullable();
            $table->timestamp('time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('develops');
    }
};
