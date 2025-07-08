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
        Schema::create('createuser', function (Blueprint $table) {    
            $table->id('id');          
            $table->integer('student_id');   
            $table->string('firstname');  
            $table->string('lastname');  
            $table->enum('gender', ['male', 'female', 'other']); 
            $table->string('yearLevel');  
            $table->string('role');     
            $table->string('block');      
            $table->string('username')->unique(); 
            $table->string('password');         
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('createuser');
    }
};
