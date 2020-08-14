<?php

use App\Models\UserEmployee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserEmployeeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      [
        'id' => 1,
        'employee_id' => 1,
        'username' => 'developer',
        'password' => Hash::make('developer'),
        'status' => 1,
        'user_id_zoom' => 'xpVW8caSTY2SPY1AcBj7aw',
        'status_generate' => 1
      ],
      [
        'id' => 2,
        'employee_id' => 2,
        'username' => 'admin',
        'password' => Hash::make('admin123'),
        'status' => 1,
        'status_generate' => 1
      ]
    ];

    foreach ($data as $item) {
      UserEmployee::updateOrCreate(['id' => $item['id']], $item);
    }
  }
}
