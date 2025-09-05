<?php

namespace App\Notifications;

use App\Models\BusinessRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class BusinessDeletedNotification extends Notification
{

    use Queueable;

    public $business;

    public $greeting;
    public $subject;
    public $first_message;
    public $closing_message;
    public $action_button;

    public $from;
    public $to;

    public $sms_cost;

    public function __construct($business)
    {
        $this->business = $business;

        $this->from = env('APP_NAME');
        $this->to = $business->user->name;



        $this->greeting = 'Hello ' . $business->user->name . '!';
        $this->subject = 'Business Deleted';
        $this->first_message = "Your business <i>'{$this->business->business_name}'</i> has been deleted by admin. <br><br>";

        $this->first_message .= "Contact support if you think this is a mistake.<br>";


        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');
        $this->action_button = [['Go home', url(route('login'))]];
    }

    public function via($notifiable)
    {
        $methods = ['database'];
        if ($notifiable->email_enabled) {
            $methods[] = 'mail';
        }

        return $methods;
    }



    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting($this->greeting)
            ->subject($this->subject)
            ->line(new HtmlString($this->first_message))
            ->action($this->action_button[0][0], $this->action_button[0][1])
            ->salutation($this->closing_message);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'greeting' => $this->greeting,
            'subject' => $this->subject,
            'first_message' => $this->first_message,
            'action_button' => $this->action_button,
            'closing_message' => $this->closing_message,
        ];
    }
}
