<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('nombre', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('tipo')->default('cliente');
            $table->string('estado')->default('activo');
            $table->string('resetToken')->nullable();
            $table->timestamp('tokenExpiracion')->nullable();
            $table->timestamps();
            
            $table->index('tipo');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};