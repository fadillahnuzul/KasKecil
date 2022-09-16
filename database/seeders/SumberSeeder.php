<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sumber;

class SumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['sumber' => 'Sumber 1'],
             ['sumber' => 'Sumber 2'],
             ['sumber' => 'Sumber 3'],
         ];
 
         foreach ($data as $value){
         Sumber::insert([
             'sumber_dana' => $value['sumber'],
         ]);
     }
    }
}
