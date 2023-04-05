<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visitor_count', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');

            $table->unsignedBigInteger('visitors');
            $table->date('date');
            $table->smallInteger('hour');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_count');
    }
};
