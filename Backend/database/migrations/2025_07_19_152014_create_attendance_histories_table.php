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
        Schema::create('attendance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id');
            $table->string('employee_id', 50);
            $table->timestamp('date_attendance');
            $table->tinyInteger('attendance_type'); // 1 In 2 Out
            $table->text('description')->nullable();

            $table->activation();
            $table->logs();

            $table->foreign('attendance_id')
                ->references('id')
                ->on('attendances')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('attendance_histories');
    }
};
