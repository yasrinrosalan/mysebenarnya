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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->timestamp('generated_at')->useCurrent();
            $table->string('report_type');
            $table->text('generated_by');
            $table->unsignedBigInteger('inquiry_id');
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->timestamps();

            $table->foreign('inquiry_id')->references('inquiry_id')->on('inquiries')->onDelete('cascade');
            $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
