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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id('inquiry_id');
            $table->string('title');
            $table->text('description');
            $table->timestamp('submitted_at')->useCurrent();
            $table->string('status')->default('pending');
            $table->text('review_notes')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('public_user_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('public_user_id')->references('user_id')->on('public_users')->onDelete('cascade');
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
