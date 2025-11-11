<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Ajouter les nouvelles colonnes nullable
            $table->string('reference')->nullable()->after('user_id');
            $table->string('label')->nullable()->after('reference');
            $table->string('currency')->default('XOF')->after('label');
            $table->boolean('is_primary')->default(false)->after('currency');
        });

        // Migrer les données existantes
        DB::statement("UPDATE wallets SET reference = 'principal', label = 'Compte Principal', currency = 'XOF', is_primary = true WHERE reference IS NULL");

        // Rendre reference obligatoire et ajouter l'index unique composite
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('reference')->nullable(false)->change();
            $table->unique(['user_id', 'reference'], 'wallets_user_reference_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique('wallets_user_reference_unique');
            $table->dropColumn(['reference', 'label', 'currency', 'is_primary']);
        });
    }
};
