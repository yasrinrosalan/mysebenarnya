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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->string('file_type');
            $table->string('url_path');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->unsignedBigInteger('inquiry_id');
            $table->timestamps();

            $table->foreign('inquiry_id')->references('inquiry_id')->on('inquiries')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
