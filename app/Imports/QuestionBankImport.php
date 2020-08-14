<?php


namespace App\Imports;

use App\Models\AnswerKey;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class QuestionBankImport implements ToModel, WithBatchInserts, WithChunkReading, WithStartRow
{
  protected $semesterId;
  protected $gradeLevelId;
  protected $subjectId;
  protected $schoolYearId;

  public function __construct($semesterId, $gradeLevelId, $subjectId, $schoolYearId)
  {
    $this->semesterId = $semesterId;
    $this->gradeLevelId = $gradeLevelId;
    $this->subjectId = $subjectId;
    $this->schoolYearId = $schoolYearId;
  }

  public function model(array $row)
  {
    if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5]) || empty($row[6])) {
      Session::put('failed', Session::get('failed') + 1);
    } else {
      switch (strtoupper($row[6])) {
        case 'B':
        case 'C':
        case 'D':
        case 'E':
        case 'A':
          $check = true;
          break;
        default:
          $check = false;
          break;
      }
      if ($check == true) {
        $employeeId = Auth::user()->employee_id;
        if (optional(configuration())->type_school == 1) {
          $question = QuestionBank::create([
            'question_name' => htmlspecialchars($row[0]),
            'subject_id' => $this->subjectId,
            'type_question' => 1,
            'semester_id' => $this->semesterId,
            'school_year_id' => $this->schoolYearId,
            'employee_id' => $employeeId,
          ]);
        } else {
          $question = QuestionBank::create([
            'question_name' => htmlspecialchars($row[0]),
            'subject_id' => $this->subjectId,
            'type_question' => 1,
            'grade_level_id' => $this->gradeLevelId,
            'school_year_id' => $this->schoolYearId,
            'employee_id' => $employeeId,
          ]);
        }

        if ($question) {
          for ($i = 1; $i < 6; $i++) {
            $alphabet = chr(ord('A') + ($i - 1));
            if ($alphabet == $row[6]) {
              AnswerKey::create([
                'answer_name' => $row[$i],
                'key' => 1,
                'question_id' => $question->id,
                'employee_id' => $employeeId,
              ]);
            } else {
              AnswerKey::create([
                'answer_name' => $row[$i],
                'key' => 0,
                'question_id' => $question->id,
                'employee_id' => $employeeId,
              ]);
            }
          }
        }
        Session::put('success', Session::get('success') + 1);
      } else {
        Session::put('failed', Session::get('failed') + 1);
      }
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
