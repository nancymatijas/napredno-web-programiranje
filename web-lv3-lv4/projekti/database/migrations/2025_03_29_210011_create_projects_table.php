<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('naziv_projekta');
            $table->text('opis_projekta');
            $table->decimal('cijena_projekta', 10, 2);
            $table->text('obavljeni_poslovi')->nullable();
            $table->date('datum_pocetka');
            $table->date('datum_zavrsetka')->nullable();
            $table->unsignedBigInteger('voditelj_id'); // ID korisnika koji je voditelj
            $table->timestamps();
    
            // Foreign key za voditelja projekta
            $table->foreign('voditelj_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
