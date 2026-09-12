<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->decimal('cantidad', 10, 2)->nullable()->after('doc_path');
            $table->string('categoria_gasto')->nullable()->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['cantidad', 'categoria_gasto']);
        });
    }
};
