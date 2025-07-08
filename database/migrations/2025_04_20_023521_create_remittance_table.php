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
        Schema::create('remittance', function (Blueprint $table) {
            $table->id('id');   
            $table->integer('student_id');     
            $table->string('firstName');
            $table->string('lastName');
            $table->string('yearLevel');  
            $table->string('block');     
            $table->text('description');
            $table->decimal('paid', 8, 2);
            $table->string('role');
            $table->date('date');
            $table->string('status');
            $table->date('date_remitted');
            $table->string('collectedBy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remittance');
    }
};
