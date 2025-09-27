<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class TransactionInvoiceNotification extends Notification
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
        $this->user = $transaction->user;

        $this->from = env('APP_NAME');
        $this->to   = $this->user->name;

        $this->greeting = 'Hello ' . $this->user->name . '!';
        $this->subject  = 'Invoice Generated for Your Transaction';

        $this->first_message  = "An invoice has been generated for your transaction request. Please make payment before it expires. <br><br>";
        $this->first_message .= "<b>Transaction Id :</b> {$transaction->trans_id} <br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";
        $this->first_message .= "<b>Amount Beneficiary Receives: </b>" .
            $transaction->fromCurrency->code . number_format($transaction->amount_to_receive, 2) . "<br>";
        $this->first_message .= "<b>Amount You Need to Pay: </b>" .
            $transaction->toCurrency->code . number_format($transaction->final_amount_to_pay, 2) . "<br><br>";

        // bank details
        $this->first_message .= "<b>Bank Name:</b> {$transaction->bank_name}<br>";
        $this->first_message .= "<b>Account Number:</b> {$transaction->account_number}<br>";
        $this->first_message .= "<b>Account Name:</b> {$transaction->account_name}<br>";
        $this->first_message .= "<b>Invoice Expires:</b> " .
            optional($transaction->invoice_expires_at)->format('d M Y H:i') . "<br><br>";

        $this->first_message .= "Click the button below to view your invoice and make payment. <br>";

        $this->closing_message = 'Regards, The Support Team ' . env('APP_NAME');
        $this->action_button   = [
            ['View Invoice', url(route('client.transactions.invoice', $transaction->id))],
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
            'from'           => $this->from,
            'to'             => $this->to,
            'greeting'       => $this->greeting,
            'subject'        => $this->subject,
            'first_message'  => $this->first_message,
            'action_button'  => $this->action_button,
            'closing_message' => $this->closing_message,
        ];
    }
}
