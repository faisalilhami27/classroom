<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class VerifyMail extends Mailable
{
  use Queueable, SerializesModels;
  private $student;

  /**
   * Create a new message instance.
   *
   * @param Student $student
   */
  public function __construct(Student $student)
  {
    $this->student = $student;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $token = Crypt::encrypt($this->student->id);
    return $this->from(env('MAIL_FROM_ADDRESS'), 'Classroom')
      ->subject('Verifikasi email classroom')
      ->view('auth.verify', compact('token'));
  }
}
