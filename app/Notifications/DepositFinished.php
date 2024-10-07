<?php

namespace App\Notifications;

use App\Models\SwhDeposit;
use Dagstuhl\SwhDepositClient\SwhDepositStatus;
//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositFinished extends Notification
{
    //use Queueable;

    private SwhDeposit $deposit;

    /**
     * Create a new notification instance.
     */
    public function __construct(SwhDeposit $deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Deposit ";
        $message = "Your deposit from {$this->deposit->created_at} ";

        switch($this->deposit->depositStatus) {
        case SwhDepositStatus::Done:
            $subject .= "Finished";
            $message .= "finished successfully.";
            break;
        case SwhDepositStatus::Failed:
            $subject .= "Failed";
            $message .= "failed.";
            break;
        case SwhDepositStatus::Rejected:
            $subject .= "Rejected";
            $message .= "was rejected.";
            break;
        }

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action("Show Deposit", route("swh-deposits.show", [ "deposit" => $this->deposit ]))
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
