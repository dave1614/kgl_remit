<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AdminUsersRegisteredNotification extends Notification
{
    use Queueable;

    public $user;
    public $date;
    public $time;

    public $greeting;
    public $subject;
    public $first_message;
    public $closing_message;
    public $action_button;

    public $from;
    public $to;

    public $sms_cost;

    public function __construct($user, $date, $time)
    {
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;

        $this->from = env('APP_NAME');
        $this->to = "admin";



        $this->greeting = 'Hello Admin!';
        $this->subject = 'New User Registration';
        $this->first_message = "A new user has just registered: <br>";
        $this->first_message .= "<b>User Name:</b> {$this->user->user_name}<br>";
        $this->first_message .= "<b>Email Address:</b> {$this->user->email}<br>";
        $this->first_message .= "<b>Phone Number:</b> {$this->user->phone}<br>";
        $this->first_message .= "<b>Date:</b> {$this->date}<br>";
        $this->first_message .= "<b>Time:</b> {$this->time}<br>";


        $this->closing_message = 'Thank you for using ' . env('APP_NAME') . '!';
        $this->action_button = [['Manage Users', url(route('admin.users.index'))]];
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
