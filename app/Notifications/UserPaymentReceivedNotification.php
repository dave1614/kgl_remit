<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class UserPaymentReceivedNotification extends Notification
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

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
        $this->user        = $transaction->user;

        $this->from = env('APP_NAME');
        $this->to   = $this->user->name;

        $this->greeting = 'Hello ' . $this->user->name . '!';
        $this->subject  = 'Payment Received for Your Transaction';

        $this->first_message  = "We have received your payment for the transaction below. It will now be processed. <br><br>";
        $this->first_message .= "<b>Transaction Id :</b> {$transaction->trans_id} <br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";
        $this->first_message .= "<b>Amount Beneficiary Receives: </b>" .
            $transaction->fromCurrency->code . number_format($transaction->amount_to_receive, 2) . "<br>";
        $this->first_message .= "<b>Amount You Paid: </b>" .
            $transaction->toCurrency->code . number_format($transaction->final_amount_to_pay, 2) . "<br><br>";

        $this->first_message .= "You can view full receipt by clicking the button below.<br>";

        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');

        $this->action_button  = [
            ['View Receipt', url(route('client.transactions.receipt', $transaction->id))],
            ['Track Transaction', url(route('client.transactions.index') . '?trans_id=' . $transaction->trans_id)]
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
            ->line(new HtmlString(
                '<a href="' . $this->action_button[1][1] . '" ' .
                    'style="
                display:inline-block;
                background-color:#1D4ED8;
                color:#ffffff;
                padding:10px 20px;
                text-decoration:none;
                border-radius:4px;
                margin-top:8px;
            ">' . $this->action_button[1][0] . '</a>'
            ))
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
