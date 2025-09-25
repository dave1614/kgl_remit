<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class UserTransactionRejectedNotification extends Notification
{
    use Queueable;

    public $transaction;
    public $user;
    public $reason;

    public $from;
    public $to;

    public $greeting;
    public $subject;
    public $first_message;
    public $closing_message;

    public $action_button;

    public function __construct($transaction, $reason)
    {
        $this->transaction = $transaction;
        $this->user        = $transaction->user;
        $this->reason      = $reason;

        $this->from = env('APP_NAME');
        $this->to   = $this->user->name;

        $this->greeting = 'Hello ' . $this->user->name . '!';
        $this->subject  = 'Your Transaction Was Rejected';

        $this->first_message  = "Unfortunately, your transaction has been rejected. <br><br>";
        $this->first_message .= "<b>Transaction ID :</b> {$transaction->trans_id} <br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";
        $this->first_message .= "<b>Amount Beneficiary Would Receive: </b>" .
            $transaction->fromCurrency->code . number_format($transaction->amount_to_receive, 2) . "<br>";
        $this->first_message .= "<b>Amount You Intended to Pay: </b>" .
            $transaction->toCurrency->code . number_format($transaction->amount_to_pay, 2) . "<br><br>";
        $this->first_message .= "<b>Reason for Rejection:</b> {$reason} <br><br>";
        $this->first_message .= "Please contact support if you have any questions about this rejection.<br>";

        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');

        $this->action_button  = [
            ['View Transaction Details', url(route('client.transactions.index').'?trans_id='. $transaction->trans_id)]
        ];
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
            'from'            => $this->from,
            'to'              => $this->to,
            'greeting'        => $this->greeting,
            'subject'         => $this->subject,
            'first_message'   => $this->first_message,
            'action_button'   => $this->action_button,
            'closing_message' => $this->closing_message,
        ];
    }
}
