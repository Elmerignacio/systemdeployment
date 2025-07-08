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
        Schema::create('createpayable', function (Blueprint $table) {
            $table->id('id');   
            $table->integer('student_id');   
            $table->string('studentName');
            $table->text('description');
            $table->decimal('amount', 8, 2);
            $table->decimal('balance', 8, 2);
            $table->date('dueDate');
            $table->string('yearLevel');
            $table->string('block');
            $table->string('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('createpayable');
    }
};
