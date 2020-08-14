<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(EmployeeSeeder::class);
    $this->call(NavigationSeeder::class);
    $this->call(RoleSeeder::class);
    $this->call(UserEmployeeSeeder::class);
    $this->call(RoleUserSeeder::class);
    $this->call(UserNavigationSeeder::class);
    $this->call(TypeSchoolSeeder::class);
    $this->call(SemesterSeeder::class);
  }
}
