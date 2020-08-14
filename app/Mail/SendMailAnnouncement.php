<?php

namespace App\Mail;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailAnnouncement extends Mailable
{
  use Queueable, SerializesModels;
  protected $announcement;

  /**
   * Create a new message instance.
   *
   * @param Announcement $announcement
   */
  public function __construct(Announcement $announcement)
  {
    $this->announcement = $announcement;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $announcement = $this->announcement;
    return $this->from(env('MAIL_FROM_ADDRESS'), 'Classroom')
      ->subject('Pengumuman Baru Classroom')
      ->view('mail.announcement', compact('announcement'));
  }
}
