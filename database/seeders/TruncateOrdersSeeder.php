<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds untuk menghapus semua data order dan order items.
     * Tidak akan mempengaruhi data lain seperti products, users, dll.
     */
    public function run(): void
    {
        // Matikan pengecekan foreign key untuk memungkinkan truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data order_items terlebih dahulu (child table)
        $this->command->info('Menghapus data order_items...');
        DB::table('order_items')->truncate();
        $this->command->info('Data order_items berhasil dihapus!');

        // Hapus data orders
        $this->command->info('Menghapus data orders...');
        DB::table('orders')->truncate();
        $this->command->info('Data orders berhasil dihapus!');

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Semua data orders dan order_items berhasil dihapus!');
        $this->command->info('Database siap untuk menerima seed order baru.');
    }
}
