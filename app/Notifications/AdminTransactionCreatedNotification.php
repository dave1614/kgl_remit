<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

class AdminTransactionCreatedNotification extends Notification
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

    public function __construct($transaction, $user, $business)
    {
        $this->transaction = $transaction;
        $this->user = $user;

        $this->from = env('APP_NAME');
        $this->to = 'admin';

        $this->greeting = 'Hello Admin!';
        $this->subject = 'New Transaction Request Submitted';

        $this->first_message  = "A new transaction request has been submitted by a business. ";
        $this->first_message .= "An invoice has also been generated and received.<br><br>";

        $this->first_message .= "<b>Transaction ID:</b> {$transaction->trans_id}<br>";
        $this->first_message .= "<b>Business Name:</b> {$business->business_name}<br>";
        $this->first_message .= "<b>User:</b> {$user->name} ({$user->email})<br>";
        $this->first_message .= "<b>From:</b> {$transaction->fromCurrency->code} ";
        $this->first_message .= "<b>To:</b> {$transaction->toCurrency->code}<br>";
        $this->first_message .= "<b>Amount Beneficiary Receives:</b> " . $transaction->fromCurrency->code . number_format($transaction->amount_to_receive, 2) . "<br>";
        $this->first_message .= "<b>Amount to Pay:</b> " . $transaction->toCurrency->code . number_format($transaction->amount_to_pay, 2) . "<br><br>";
        $this->first_message .= "Please review and approve/reject in the admin panel.<br>";

        $this->closing_message = 'Thank you for using ' . env('APP_NAME') . '!';

        // ✅ Generate a public invoice file URL from storage
        $invoiceUrl = null;
        if (!empty($transaction->business_invoice_path)) {
            // make sure you’re using `php artisan storage:link`
            $invoiceUrl = asset('storage/' . $transaction->business_invoice_path);
        }

        // ✅ Define both buttons
        $this->action_button = [
            ['Review Transaction', url(route('admin.transactions.index') . '?trans_id=' . $transaction->trans_id)],
        ];

        if ($invoiceUrl) {
            $this->action_button[] = ['View Invoice', $invoiceUrl];
        }
    }

    public function via($notifiable)
    {
        $methods = ['database'];
        if ($notifiable->email_enabled) $methods[] = 'mail';
        return $methods;
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->greeting($this->greeting)
            ->subject($this->subject)
            ->line(new HtmlString($this->first_message))
            ->action($this->action_button[0][0], $this->action_button[0][1]);

        // ✅ Add second “View Invoice” button if invoice file exists
        if (isset($this->action_button[1])) {
            $mail->line(new HtmlString(
                '<a href="' . $this->action_button[1][1] . '"
                    style="
                        display:inline-block;
                        background-color:#1D4ED8;
                        color:#ffffff;
                        padding:10px 20px;
                        text-decoration:none;
                        border-radius:4px;
                        margin-top:8px;
                    ">'.$this->action_button[1][0].'</a>'
            ));
        }

        return $mail->salutation($this->closing_message);
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
