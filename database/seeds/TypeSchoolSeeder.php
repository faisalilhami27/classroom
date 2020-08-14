<?php

use App\Models\TypeSchool;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TypeSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $roles = [
        [
          'id' => 1,
          'name' => 'Universitas',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ],
        [
          'id' => 2,
          'name' => 'SMA/SMK/MA',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ],
        [
          'id' => 3,
          'name' => 'SMP/MTS',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ],
        [
          'id' => 4,
          'name' => 'SD/MI',
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]
      ];

      foreach ($roles as $role) {
        TypeSchool::updateOrCreate(['id' => $role['id']], $role);
      }
    }
}
