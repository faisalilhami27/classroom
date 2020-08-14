<?php


namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SubjectImport implements ToModel, WithBatchInserts, WithChunkReading, WithStartRow
{
  protected $major;

  public function __construct($major)
  {
    $this->major = $major;
  }

  public function model(array $row)
  {
    if (optional(configuration())->type_school == 1) {
      Subject::updateOrCreate(['code' => $row[0]], [
        'code' => htmlspecialchars($row[0]),
        'name' => htmlspecialchars($row[1]),
        'semester_id' => htmlspecialchars($row[2]),
        'major_id' => htmlspecialchars($this->major)
      ]);
    } else if (optional(configuration())->type_school == 2) {
      $majorId = (empty($row[2])) ? $this->major :  $row[2];
      Subject::updateOrCreate(['code' => $row[0]], [
        'code' => htmlspecialchars($row[0]),
        'name' => htmlspecialchars($row[1]),
        'major_id' => htmlspecialchars($majorId)
      ]);
    } else {
      Subject::updateOrCreate(['code' => $row[0]], [
        'code' => htmlspecialchars($row[0]),
        'name' => htmlspecialchars($row[1]),
      ]);
    }
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
