<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diretorias', function (Blueprint $table) {
            $table->unsignedInteger('ordem')->default(0)->after('icon');
        });

        // Preenche a ordem inicial pela ordem de criação, pra manter a
        // sequência atual (Presidência, Secretaria, ...) já visível hoje.
        DB::table('diretorias')->orderBy('id')->get()->each(function ($dir, $i) {
            DB::table('diretorias')->where('id', $dir->id)->update(['ordem' => $i]);
        });
    }

    public function down(): void
    {
        Schema::table('diretorias', function (Blueprint $table) {
            $table->dropColumn('ordem');
        });
    }
};