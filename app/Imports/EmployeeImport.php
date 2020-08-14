<?php


namespace App\Imports;


use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EmployeeImport implements ToModel, WithBatchInserts, WithChunkReading, WithStartRow
{

  public function model(array $row)
  {
    Employee::updateOrCreate(['employee_identity_number'=> $row[0]], [
      'name' => $row[1] . ' ' . $row[2],
      'first_name' => $row[1],
      'last_name' => $row[2],
      'email' => $row[1],
      'phone_number' => $row[2],
    ]);
  }

  public function batchSize(): int
  {
    return 500;
  }

  public function chunkSize(): int
  {
    return 500;
  }

  public function startRow(): int
  {
    return 2;
  }
}
