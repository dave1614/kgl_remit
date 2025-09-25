<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Notifications\UserTransactionExpiredNotification;

class ExpireTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark expired transactions automatically';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiredTransactions = Transaction::where('status', 'pending_payment')
            ->where('invoice_expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredTransactions as $transaction) {
            $transaction->update(['status' => 'expired']);
            $transaction->user->notify(new UserTransactionExpiredNotification($transaction));
            $count++;
        }

        $this->info("Expired {$count} transactions.");
    }
}
