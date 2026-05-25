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
    Schema::table('users', function (Blueprint $table) {
        // Campos de Persona [cite: 146]
        $table->string('apellido_paterno')->after('name');
        $table->string('apellido_materno')->after('apellido_paterno');
        $table->string('ci')->unique()->after('id');
        $table->string('telefono')->nullable();

        // Campos de Usuario/Rol [cite: 221, 228]
        $table->enum('rol', ['admin', 'recepcionista', 'enfermera', 'medico'])->default('recepcionista');
        $table->string('especialidad')->nullable(); // Solo para médicos [cite: 227]
        $table->string('cargo')->nullable();
        $table->boolean('activo')->default(true);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
