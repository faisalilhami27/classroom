<?php

use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
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
        'number' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 2,
        'number' => 2,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 3,
        'number' => 3,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 4,
        'number' => 4,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 5,
        'number' => 5,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 6,
        'number' => 6,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 7,
        'number' => 7,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ],
      [
        'id' => 8,
        'number' => 8,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ]
    ];

    foreach ($roles as $role) {
      Semester::updateOrCreate(['id' => $role['id']], $role);
    }
  }
}
