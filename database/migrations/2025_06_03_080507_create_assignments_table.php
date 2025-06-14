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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->string('status')->default('assigned');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('last_updated_at')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('inquiry_id');
            $table->unsignedBigInteger('agency_user_id');
            $table->timestamps();

            $table->foreign('inquiry_id')->references('inquiry_id')->on('inquiries')->onDelete('cascade');
            $table->foreign('agency_user_id')->references('user_id')->on('agency_users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
