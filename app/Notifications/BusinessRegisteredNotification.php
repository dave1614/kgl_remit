<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class BusinessRegisteredNotification extends Notification
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

    public function __construct($user, $business)
    {
        $this->business = $business;

        $this->from = env('APP_NAME');
        $this->to = $user->name;



        $this->greeting = 'Hello ' . $user->name . '!';
        $this->subject = 'Your Business Registration is Pending Approval';
        $this->first_message = "Thank you for submitting your business registration with us. <br><br>";
        $this->first_message .= "<b>Business Name:</b> {$this->business->business_name}<br>";
        $this->first_message .= "Your documents have been uploaded and are currently under review by our admin team. <br>";
        $this->first_message .= "You will receive another notification once your registration has been approved or rejected. <br><br>";

        $this->first_message .= "We appreciate your patience.<br>";


        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');
        $this->action_button = [['View Status', url(route('client.business.limbo', 'pending'))]];
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
