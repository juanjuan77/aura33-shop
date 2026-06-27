<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearConsignmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('consignment_payments')->truncate();
        DB::table('consignment_items')->truncate();
        DB::table('consignments')->truncate();
        echo "Consignaciones limpiadas.\n";
    }
}
