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
        Schema::table('student_task', function (Blueprint $table) {
            if (!Schema::hasColumn('student_task', 'status')) {
                $table->string('status')->default('pending')->after('task_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('student_task', function (Blueprint $table) {
            if (Schema::hasColumn('student_task', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
