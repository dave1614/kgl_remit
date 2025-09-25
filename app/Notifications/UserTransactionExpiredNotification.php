<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class UserTransactionExpiredNotification extends Notification
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

    /**
     * Create a new notification instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
        $this->user        = $transaction->user;

        $this->from = env('APP_NAME');
        $this->to   = $this->user->name;

        $this->greeting = 'Hello ' . $this->user->name . '!';
        $this->subject  = 'Your Transaction Invoice Has Expired';

        $this->first_message  = "Unfortunately, your transaction invoice has expired because payment was not received before the deadline. <br><br>";
        $this->first_message .= "<b>Transaction ID :</b> {$transaction->trans_id} <br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";
        $this->first_message .= "<b>Amount Beneficiary Would Receive: </b>" .
            $transaction->fromCurrency->code . number_format($transaction->amount_to_receive, 2) . "<br>";
        $this->first_message .= "<b>Amount You Needed to Pay: </b>" .
            $transaction->toCurrency->code . number_format($transaction->final_amount_to_pay, 2) . "<br><br>";

        $this->first_message .= "If you still wish to complete this transaction, you will need to create a new request. <br>";

        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');

        // optional button to dashboard or new transaction page
        $this->action_button  = [
            ['Create New Transaction', url(route('client.transactions.create'))]
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
