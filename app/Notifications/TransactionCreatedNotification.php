<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class TransactionCreatedNotification extends Notification
{
    use Queueable;

    public $transaction;
    public $user;
    public $from;
    public $to;

    public $greeting;
    public $subject;
    public $first_message;
    public $closing_message;
    public $action_button;

    public function __construct($transaction, $user)
    {
        $this->transaction = $transaction;
        $this->user = $user;

        $this->from = env('APP_NAME');
        $this->to   = $user->name;

        $this->greeting = 'Hello ' . $user->name . '!';
        $this->subject = 'Your Transaction Request Has Been Submitted';
        $this->first_message = "We’ve received your transaction request. <br><br>";
        $this->first_message .= "<b>Transaction Id :</b> {$transaction->trans_id} <br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";
        $this->first_message .= "<b>Amount Beneficiary Receives: </b>".$transaction->fromCurrency->code .  number_format($transaction->amount_to_receive, 2) ."<br>";
        $this->first_message .= "<b>Amount You Will Pay: </b>".$transaction->toCurrency->code. number_format($transaction->amount_to_pay) ."<br><br>";
        $this->first_message .= "You’ll be notified once an invoice is generated or if further action is required.<br>";

        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');
        $this->action_button = [['View Transaction', url(route('client.transactions.index'). '?trans_id='. $transaction->trans_id)]];
    }

    public function via($notifiable)
    {
        $methods = ['database'];
        if ($notifiable->email_enabled) $methods[] = 'mail';
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

    public function toArray($notifiable)
    {
        return [
            'from' => $this->from,
            'to'   => $this->to,
            'greeting' => $this->greeting,
            'subject' => $this->subject,
            'first_message' => $this->first_message,
            'action_button' => $this->action_button,
            'closing_message' => $this->closing_message,
        ];
    }
}
