<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_alergias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();

            $table->enum('tipo', ['medicamento', 'alimento', 'ambiental', 'otro'])
                ->default('medicamento');

            // Nombre libre: "Penicilina", "Polen", "Mariscos", etc.
            $table->string('descripcion', 255);

            // Solo si tipo = 'medicamento' y ya existe en la tabla medicamentos
            $table->foreignId('medicamento_id')
                ->nullable()
                ->constrained('medicamentos')
                ->nullOnDelete();

            $table->enum('severidad', ['leve', 'moderada', 'grave'])
                ->nullable();

            // Descripción de la reacción: urticaria, anafilaxia, etc.
            $table->text('reaccion')->nullable();

            $table->foreignId('registrado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_alergias');
    }
};