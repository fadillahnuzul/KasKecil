<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Divisi;


class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Schema::disableForeignKeyConstraints();
        // Divisi::truncate();
        // Schema::enableForeignKeyConstraints();

        $data = [
           ['divisi' => 'ICT'],
            ['divisi' => 'Finance'],
            ['divisi' => 'Accounting'],
        ];

        foreach ($data as $value){
        Divisi::insert([
            'nama_divisi' => $value['divisi'],
        ]);
    }
    }
}
