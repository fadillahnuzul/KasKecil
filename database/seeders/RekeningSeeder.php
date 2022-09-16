<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rekening;

class RekeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['nama' => 'BRI'],
             ['nama' => 'BNI'],
             ['nama' => 'Mandiri'],
         ];
 
         foreach ($data as $value){
         Rekening::insert([
             'nama_rekening' => $value['nama'],
         ]);
     }
    }
}
