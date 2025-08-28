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
        Schema::create('preferred_foci', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('preferred_foci')->insert([
            ['name' => 'speaking', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'listening', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'grammar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'vocab', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferred_foci');
    }
};
