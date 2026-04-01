<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newStatuses = ['ready to start', 'in_progress', 'done', 'waiting for review', 'pending'];

        // 1. HAPUS SEMUA CONSTRAINT LAMA YANG MUNGKIN ADA (Penting untuk Postgres)
        DB::statement("ALTER TABLE todos DROP CONSTRAINT IF EXISTS todos_status_check");
        DB::statement("ALTER TABLE todos DROP CONSTRAINT IF EXISTS check_status_enum");

        // 2. Ubah kolom jadi string
        Schema::table('todos', function (Blueprint $table) {
            $table->string('status')->change();
        });

        // 3. Mapping data
        DB::table('todos')->where('status', 'open')->update(['status' => 'ready to start']);
        DB::table('todos')->where('status', 'completed')->update(['status' => 'done']);

        // 4. Tambahkan constraint baru yang bersih
        $checkConstraint = implode("', '", $newStatuses);
        DB::statement("ALTER TABLE todos ADD CONSTRAINT check_status_enum CHECK (status IN ('$checkConstraint'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus constraint jika ada
        DB::statement("ALTER TABLE todos DROP CONSTRAINT IF EXISTS check_status_enum");

        Schema::table('todos', function (Blueprint $table) {
            $table->string('status')->change();
        });

        DB::table('todos')->where('status', 'ready to start')->update(['status' => 'open']);
        DB::table('todos')->where('status', 'done')->update(['status' => 'completed']);

        Schema::table('todos', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};