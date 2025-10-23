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
        Schema::create('inspecaos', function (Blueprint $table) {
            $table->id();
            $table->date('data_inspecao');
            $table->boolean('viu_rainha')->default(false);
            $table->integer('nivel_populacao');
            $table->integer('reservas_mel');
            $table->boolean('sinais_parasitas')->default(false);
            $table->text('observacoes')->nullable();
            $table->foreignId('colmeia_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspecaos');
    }
};
