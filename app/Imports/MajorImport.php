<?php


namespace App\Imports;


use App\Models\Major;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MajorImport implements ToModel, WithBatchInserts, WithChunkReading, WithStartRow
{

  public function model(array $row)
  {
    Major::updateOrCreate(['code'=> $row[0]], [
      'code' => $row[0],
      'name' => $row[1],
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
