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
        Schema::create('denomination', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('thousand')->default(0);   
            $table->integer('five_hundred')->default(0); 
            $table->integer('two_hundred')->default(0);  
            $table->integer('one_hundred')->default(0); 
            $table->integer('fifty')->default(0);       
            $table->integer('twenty')->default(0);      
            $table->integer('ten')->default(0);          
            $table->integer('five')->default(0);     
            $table->integer('one')->default(0);          
            $table->integer('twenty_five_cents')->default(0);
            $table->decimal('totalAmount', 10, 2);
            $table->string('collectedBy');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denomination');
    }
};
