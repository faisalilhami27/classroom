<?php

namespace App\Jobs;

use App\Mail\VerifyMail;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class VerifyEmailRegisterJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $student;
  protected $email;

  /**
   * Create a new job instance.
   *
   * @param $email
   * @param Student $student
   */
  public function __construct($email, Student $student)
  {
    $this->email = $email;
    $this->student = $student;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $email = new VerifyMail($this->student);
    Mail::to($this->email)->send($email);
  }
}
