<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => '1', 'nama' => 'Wait for approval'],
            ['id' => '2', 'nama' => 'Approved'],
            ['id' => '3', 'nama' => 'Declined'],
            ['id' => '4', 'nama' => 'Wait for responbility'],
            ['id' => '5', 'nama' => 'Done'],
         ];
 
         foreach ($data as $value){
         Status::insert([
             'id' => $value['id'],
             'nama_status' => $value['nama'],
         ]);
     }
    }
}
