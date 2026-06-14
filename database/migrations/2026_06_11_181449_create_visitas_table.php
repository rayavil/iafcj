<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono')->nullable();
            $table->unsignedTinyInteger('edad')->nullable();
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->nullOnDelete();
            $table->foreignId('padre_espiritual_id')->nullable()->constrained('padres_espirituales')->nullOnDelete();
            $table->string('padre_espiritual_otro')->nullable();
            $table->enum('estatus', ['nuevo', 'contactado', 'integrado', 'sin_respuesta'])->default('nuevo');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
