<?php


namespace App\Traits;


use App\Jobs\SendMailJob;
use App\Models\Employee;
use App\Models\Student;
use App\Models\StudentClassTransaction;
use Pusher\Pusher;

trait CustomTrait
{
  /**
   * pusher configuration
   * @param $data
   * @throws \Pusher\PusherException
   */
  public function pusherConfig($data = null)
  {
    $options = ['cluster' => env('PUSHER_APP_CLUSTER')];
    $pusher = new Pusher(
      env('PUSHER_APP_KEY'),
      env('PUSHER_APP_SECRET'),
      env('PUSHER_APP_ID'),
      $options
    );

    $pusher->trigger('my-channel', 'my-event', $data);
  }

  /**
   * send email to student
   * @param $classIdOrStudents
   * @param $announcement
   * @param $type
   */
  public function sendMail($classIdOrStudents, $announcement, $type = null)
  {
   if ($type == 'posting') {
     $studentClassTransactions = StudentClassTransaction::where('class_id', $classIdOrStudents)->get();
     foreach ($studentClassTransactions as $studentClassTransaction) {
       dispatch(new SendMailJob($studentClassTransaction->student->email, $announcement));
     }
   } else {
     foreach ($classIdOrStudents as $list) {
       $student = Student::where('id', $list)->first();
       dispatch(new SendMailJob($student->email, $announcement));
     }
   }
  }

  /**
   * get username for notification
   * @param $data
   * @return array
   */
  public function getUsername($data)
  {
    $array = [];
    if (!is_null($data)) {
      foreach ($data as $item) {
        $users = (object) $item;
        if (is_null($users->student_id)) {
          $employee = Employee::where('id', $users->employee_id)->first();
          $array[] = [
            'username' => $employee->userEmployee->username
          ];
        } else {
          $student = Student::where('id', $users->student_id)->first();
          $array[] = [
            'username' => $student->userStudent->username
          ];
        }
      }
    }
    return $array;
  }
}
