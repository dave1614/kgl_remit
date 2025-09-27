<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class UserPaymentProofUploadedNotification extends Notification
{
    use Queueable;

    public $transaction;
    public $user;
    public $business;

    public $from;
    public $to;
    public $greeting;
    public $subject;
    public $first_message;
    public $closing_message;
    public $action_button;

    public function __construct($transaction, $user, $business)
    {
        $this->transaction = $transaction;
        $this->user        = $user;
        $this->business    = $business;

        $this->from = env('APP_NAME');
        $this->to   = $user->name;

        $this->greeting = 'Hello ' . $user->name . '!';
        $this->subject  = 'Payment Proof Submitted for Your Transaction';

        $this->first_message  = "We have received the payment proof you uploaded for your transaction. "
            . "Our team will now verify it. Below are your transaction details:<br><br>";
        $this->first_message .= "<b>Transaction Id:</b> {$transaction->trans_id}<br>";
        $this->first_message .= "<b>Business name:</b> {$business->business_name}<br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";

        $this->first_message .= "<b>Amount Beneficiary Receives:</b> "
            . $transaction->fromCurrency->code . number_format($transaction->amount_to_receive, 2) . "<br>";

        $this->first_message .= "<b>Amount Paid:</b> "
            . $transaction->toCurrency->code . number_format($transaction->final_amount_to_pay, 2) . "<br><br>";

        $this->first_message .= "You can track the status of your transaction any time by clicking the button below.<br>";

        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');

        $this->action_button = [
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
