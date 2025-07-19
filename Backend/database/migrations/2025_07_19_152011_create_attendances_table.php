<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('attendance_id', 100);
            $table->string('employee_id', 50);
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();

            $table->activation();
            $table->logs();

            $table->foreign('employee_id')
                ->references('employee_id')
                ->on('employees')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
