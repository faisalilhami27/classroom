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
    Employee::create([
      'name' => $row[0] . ' ' . $row[1],
      'first_name' => $row[0],
      'last_name' => $row[1],
      'email' => $row[2],
      'phone_number' => $row[3],
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
