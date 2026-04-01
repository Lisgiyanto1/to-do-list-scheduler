<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Hapus default value agar tidak mengunci kolom
        DB::statement("ALTER TABLE todos ALTER COLUMN priority DROP DEFAULT");

        // 2. PENTING: Hapus constraint lama jika sudah ada agar tidak bentrok (Error SQLSTATE[42710])
        DB::statement("ALTER TABLE todos DROP CONSTRAINT IF EXISTS todos_priority_check");

        // 3. Pastikan tipe data kolom adalah VARCHAR agar bisa menampung string baru
        DB::statement("ALTER TABLE todos 
            ALTER COLUMN priority TYPE VARCHAR(255) 
            USING priority::VARCHAR(255)");

        // 4. Tambahkan kembali constraint dengan daftar pilihan yang baru (termasuk 'best effort' & 'critical')
        DB::statement("ALTER TABLE todos 
            ADD CONSTRAINT todos_priority_check 
            CHECK (priority IN ('low', 'medium', 'high', 'best effort', 'critical'))");

        // 5. Set kembali default value
        DB::statement("ALTER TABLE todos ALTER COLUMN priority SET DEFAULT 'medium'");
    }

    public function down(): void
    {
        // Kembalikan ke kondisi awal
        DB::statement("ALTER TABLE todos DROP CONSTRAINT IF EXISTS todos_priority_check");

        DB::statement("ALTER TABLE todos 
            ADD CONSTRAINT todos_priority_check 
            CHECK (priority IN ('low', 'medium', 'high'))");
    }
};