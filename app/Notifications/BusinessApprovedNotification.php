<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\BusinessRegistration;
use Illuminate\Support\HtmlString;

class BusinessApprovedNotification extends Notification
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

    public function __construct(BusinessRegistration $business)
    {
        $this->business = $business;

        $this->from = env('APP_NAME');
        $this->to = $business->user->name;



        $this->greeting = 'Hello ' . $business->user->name . '!';
        $this->subject = 'Business Approved';
        $this->first_message = "Your business <i>'{$this->business->business_name}'</i> has been approved. <br><br>";

        $this->first_message .= "Thank you for using our platform!.<br>";


        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');
        $this->action_button = [['Go to Dashboard', url(route('client.dashboard'))]];
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
