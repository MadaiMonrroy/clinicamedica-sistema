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
    Schema::create('pacientes', function (Blueprint $table) {
        $table->id(); // Identificador único interno
        
        // 1. Datos Personales
        $table->string('ci')->unique(); 
        $table->string('nombres');
        $table->string('apellido_paterno');
        $table->string('apellido_materno')->nullable(); // Nullable por si alguien tiene un solo apellido
        $table->string('telefono')->nullable();
        
        // 2. Datos Médicos/Demográficos que pediste revisar 
        $table->date('fecha_nacimiento'); 
        $table->enum('sexo', ['M', 'F']); 
        
        // 3. Auditoría: ¿Qué usuario (Recepcionista) registró a este paciente? 
        // Esto crea la relación (Foreign Key) con la tabla users
        $table->foreignId('user_id')->constrained('users');
        
        // 4. Fechas de registro
        // Esto crea mágicamente 'created_at' (tu FechaRegistro) y 'updated_at' 
        $table->timestamps(); 
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
