<?php

namespace App\Notifications;

use App\Models\SwhArchive;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveRequestStatus;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveTaskStatus;
//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArchiveFinished extends Notification
{
    //use Queueable;

    private SwhArchive $archive;

    /**
     * Create a new notification instance.
     */
    public function __construct(SwhArchive $archive)
    {
        $this->archive = $archive;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Archival Request ";
        $message = "Your archival request of {$this->archive->originUrl} from {$this->archive->created_at} ";

        switch($this->archive->saveRequestStatus) {
        case SaveRequestStatus::ACCEPTED:
            switch($this->archive->saveTaskStatus) {
            case SaveTaskStatus::SUCCEEDED:
                $subject .= "Finished";
                $message .= "finished successfully.";
                break;
            case SaveTaskStatus::FAILED:
                $subject .= "Failed";
                $message .= "failed.";
                break;
            }
            break;
        case SaveRequestStatus::REJECTED:
            $subject .= "Rejected";
            $message .= "was rejected.";
            break;
        }

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action("Show Archives", route("swh-archives.index"))
            ->line("Thank you for using our application!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
