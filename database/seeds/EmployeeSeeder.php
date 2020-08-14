<?php

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
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
        'employee_identity_number' => 1233456789,
        'name' => 'Developer',
        'first_name' => 'Developer',
        'last_name' => null,
        'email' => 'dev@gmail.com',
        'phone_number' => null,
        'photo' => null
      ],
      [
        'id' => 2,
        'employee_identity_number' => 1233456780,
        'name' => 'Administrator',
        'first_name' => 'Administrator',
        'last_name' => null,
        'email' => 'admin@gmail.com',
        'phone_number' => null,
        'photo' => null
      ]
    ];

    foreach ($data as $item) {
      Employee::updateOrCreate(['id' => $item['id']], $item);
    }
  }
}
