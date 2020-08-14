<?php


namespace App\Exports;


use App\Models\AnswerKey;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class QuestionBankExport implements FromView
{
  use Exportable;

  protected $levelId;
  protected $subjectId;

  public function __construct($levelId, $subjectId)
  {
    $this->levelId = $levelId;
    $this->subjectId = $subjectId;
  }

  public function view(): View
  {
    $subjectId = $this->subjectId;
    $subject = Subject::find($subjectId);

    if ($this->levelId == 'all' || $this->subjectId == 'all') {
      $questionData = QuestionBank::with(['subject', 'semester', 'gradeLevel'])->get();
    } else {
      $questionBank = QuestionBank::with(['subject', 'semester', 'gradeLevel'])
        ->where('subject_id', $this->subjectId);

      if (optional(configuration())->type_school == 1) {
        $questionBank->where('semester_id', $this->levelId);
      } else {
        $questionBank->where('grade_level_id', $this->levelId);
      }

      $questionData = $questionBank->get();
    }

    $data = [];
    foreach ($questionData as $question) {
      $row = [];
      $answers = AnswerKey::where('question_id', $question->id)->get();
      $questionReplace = trim(preg_replace('/\s\s+/', ' ', $question->question_name));
      $replaceAgain = html_entity_decode($questionReplace, ENT_QUOTES, "UTF-8");
      $row['question_name'] = strip_tags($replaceAgain, '<br>');
      $row['subject'] = $question->subject->name;
      if (optional(configuration())->type_school == 1) {
        $row['level'] = 'Semester ' . $question->semester->number;
      } else {
        $row['level'] = optional($question->gradeLevel)->name;
      }

      if (empty($answers)) {
        $row['answer0'] = "-";
        $row['answer1'] = "-";
        $row['answer2'] = "-";
        $row['answer3'] = "-";
        $row['answer4'] = "-";
        $row['answer'] = "-";
      } else {
        for ($i = 0; $i < 5; $i++) {
          if (empty($answers[$i])) {
            $row['answer' . $i] = "-";
            $row['answer'] = "-";
          } else {
            $row['answer' . $i] = $answers[$i]['answer_name'];
            if ($answers[$i]['key'] == 1) {
              switch ($i) {
                case 0:
                  $row['answer'] = "A";
                  break;
                case 1:
                  $row['answer'] = "B";
                  break;
                case 2:
                  $row['answer'] = "C";
                  break;
                case 3:
                  $row['answer'] = "D";
                  break;
                case 4:
                  $row['answer'] = "E";
                  break;
                default:
                  $row['answer'] = "-";
                  break;
              }
            }
          }
        }
      }

      if (empty($question->document) || is_null($question->document)) {
        $row['document'] = '-';
      } else {
        if ($question->extension == 'mp3' || $question->extension == 'ogg' || $question->extension == 'wav') {
          $row['document'] = 'Audio';
        } else if ($question->extension == 'mp4' || $question->extension == 'mkv' || $question->extension == 'm4a') {
          $row['document'] = 'Video';
        } else {
          $row['document'] = '-';
        }
      }
      $data[] = (object) $row;
    }

    return view('backend.cbt.excel', compact('data', 'subject', 'subjectId'));
  }
}
