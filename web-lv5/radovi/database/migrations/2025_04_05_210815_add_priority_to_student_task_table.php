<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_task', function (Blueprint $table) {
            // Dodajte kolonu samo ako ne postoji
            if (!Schema::hasColumn('student_task', 'priority')) {
                $table->unsignedTinyInteger('priority')
                    ->after('status')
                    ->default(0)
                    ->comment('Prioritet prijave (1-5)');
            }

            // Dodajte unique constraint samo ako ne postoji
            if (!Schema::hasIndex('student_task', 'student_task_student_id_priority_unique')) {
                $table->unique(['student_id', 'priority'], 'student_task_student_id_priority_unique');
            }
        });
    }

    public function down()
    {
        Schema::table('student_task', function (Blueprint $table) {
            // Ukloni unique constraint
            $table->dropUnique('student_task_student_id_priority_unique');
            
            // Ukloni kolonu
            $table->dropColumn('priority');
        });
    }
};
