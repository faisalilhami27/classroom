<?php

use App\Models\RoleUser;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
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
        'role_id' => 1,
        'user_employee_id' => 1,
      ],
      [
        'id' => 2,
        'role_id' => 2,
        'user_employee_id' => 2,
      ]
    ];

    foreach ($data as $item) {
      RoleUser::updateOrCreate(['id' => $item['id']], $item);
    }
  }
}
