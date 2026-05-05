<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

class SystemClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:clean {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the system by removing transaction data and entities while keeping products and categories.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('You must use the --force option to run this command in production.');
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('Are you sure you want to clean the system? This will delete all sales, purchases, customers, and transactions!')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        $this->info('Starting system cleanup...');

        Schema::disableForeignKeyConstraints();

        $tables = [
            'sale_items',
            'sales',
            'purchase_items',
            'purchases',
            'finance_transactions',
            'customers',
            'suppliers',
        ];

        foreach ($tables as $table) {
            $this->info("Truncating table: {$table}");
            DB::table($table)->truncate();
        }

        $this->info('Resetting product quantities to 0...');
        Product::query()->update(['quantity' => 0]);

        Schema::enableForeignKeyConstraints();

        $this->success('System cleanup completed successfully!');
        
        return 0;
    }

    /**
     * Success message helper.
     */
    protected function success($message)
    {
        $this->output->writeln("<info>{$message}</info>");
    }
}
