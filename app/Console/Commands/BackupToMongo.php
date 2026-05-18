<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class BackupToMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:mongo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup SQLite data to MongoDB Cloud cluster';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check for MongoDB extension
        if (!extension_loaded('mongodb')) {
            $this->error('The "mongodb" PHP extension is not loaded.');
            $this->info('Please download "php_mongodb.dll" for PHP 8.2 (x64, Thread Safe) and add it to your "ext" directory.');
            $this->info('Then add "extension=mongodb" to your php.ini file.');
            return 1;
        }

        $this->info('Starting backup process to MongoDB Atlas...');

        try {
            // 1. Backup Products
            $this->backupCollection(Product::class, 'products');
            
            // 2. Backup Customers
            $this->backupCollection(Customer::class, 'customers');
            
            // 3. Backup Sales (with items)
            $this->backupSales();

            $this->info('SUCCESS: Cloud backup completed successfully!');
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Backup a model to a MongoDB collection
     */
    private function backupCollection($modelClass, $collectionName)
    {
        $this->comment("Syncing {$collectionName}...");
        $items = $modelClass::all()->toArray();
        
        $count = 0;
        foreach ($items as $item) {
            DB::connection('mongodb')->table($collectionName)->updateOrInsert(
                ['id' => $item['id']],
                $item
            );
            $count++;
        }
        $this->info("Done: {$count} items synced to collection '{$collectionName}'.");
    }

    /**
     * Backup Sales with nested items
     */
    private function backupSales()
    {
        $this->comment("Syncing sales (with items)...");
        $sales = Sale::with('items')->get()->toArray();
        
        $count = 0;
        foreach ($sales as $sale) {
            // MongoDB allows nested documents, which is perfect for items
            DB::connection('mongodb')->table('sales')->updateOrInsert(
                ['id' => $sale['id']],
                $sale
            );
            $count++;
        }
        $this->info("Done: {$count} sales synced to collection 'sales'.");
    }
}
