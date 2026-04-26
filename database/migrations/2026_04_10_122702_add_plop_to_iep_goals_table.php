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
    Schema::table('iep_goals', function (Blueprint $table) {
        // Adds the column right after the domain column
        $table->text('plop')->nullable()->after('domain'); 
    });
}

public function down()
{
    Schema::table('iep_goals', function (Blueprint $table) {
        $table->dropColumn('plop');
    });
}
};
