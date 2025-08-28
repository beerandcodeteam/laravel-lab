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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained();
            $table->string('type')->comment('LISTENING, WRITING, MCQ, VOCAB');
            $table->string('question');
            $table->string('question_audio_path')->nullable();
            $table->json('options')->nullable();
            $table->string('answer')->nullable();
            $table->string('answer_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
