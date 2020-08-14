<?php

namespace App\Jobs;

use App\Mail\SendMailAnnouncement;
use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $emails;
  protected $announcement;

  /**
   * Create a new job instance.
   *
   * @param $emails
   * @param Announcement $announcement
   */
  public function __construct($emails, Announcement $announcement)
  {
    $this->emails = $emails;
    $this->announcement = $announcement;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $email = new SendMailAnnouncement($this->announcement);
    Mail::to($this->emails)->send($email);
  }
}
