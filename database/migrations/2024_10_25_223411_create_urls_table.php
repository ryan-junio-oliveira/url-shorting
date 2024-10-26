<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->string('original_url')->unique();
            $table->string('short_url')->unique();
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
