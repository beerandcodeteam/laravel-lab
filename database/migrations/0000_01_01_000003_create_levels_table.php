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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::table('levels')->insert([
            [
                'name' => 'A1',
                'description' => 'Compreende e usa expressões cotidianas muito simples; consegue apresentar-se, fazer e responder perguntas básicas sobre dados pessoais; interage de forma limitada quando o interlocutor fala devagar e com clareza.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'A2',
                'description' => 'Lida com tarefas simples e rotineiras; entende e produz frases sobre temas imediatos (família, compras, trabalho); descreve de forma básica passado e planos próximos.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'B1',
                'description' => 'Entende pontos principais de textos/áudios claros; mantém conversas sobre assuntos familiares; descreve experiências, planos e opiniões de forma simples e coerente.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'B2',
                'description' => 'Interage com espontaneidade e relativa fluência; discute temas variados; redige textos claros e detalhados; defende pontos de vista com argumentos apropriados.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'C1',
                'description' => 'Compreende textos longos e exigentes; expressa-se de forma fluente e espontânea; usa linguagem flexível e eficaz para fins acadêmicos e profissionais.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'C2',
                'description' => 'Compreende praticamente tudo que ouve ou lê; resume e reconstrói informações de diferentes fontes; comunica-se com precisão, nuance e naturalidade em qualquer situação.',
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
