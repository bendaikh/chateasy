<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CleanFakeProducts extends Command
{
    protected $signature = 'products:clean-fake';

    protected $description = 'Remove fake/seeded products from database (products with placeholder images)';

    public function handle()
    {
        $this->info('Searching for fake/seeded products...');
        
        // Find products with placeholder images (via.placeholder.com)
        $fakeProducts = Product::where(function($query) {
            $query->whereRaw("JSON_EXTRACT(images, '$[0]') LIKE ?", ['%via.placeholder.com%'])
                  ->orWhereRaw("JSON_EXTRACT(images, '$[0]') LIKE ?", ['%placeholder%']);
        })->get();
        
        if ($fakeProducts->count() === 0) {
            $this->info('No fake products found in the database.');
            return 0;
        }
        
        $this->warn("Found {$fakeProducts->count()} fake products:");
        foreach ($fakeProducts as $product) {
            $this->line("  - {$product->name} (ID: {$product->id})");
        }
        
        if ($this->confirm('Do you want to delete these fake products?', true)) {
            $deleted = 0;
            foreach ($fakeProducts as $product) {
                $product->delete();
                $deleted++;
            }
            
            $this->info("Successfully deleted {$deleted} fake products!");
            $this->info('Your website will now only show real products you add.');
        } else {
            $this->info('Operation cancelled.');
        }
        
        return 0;
    }
}
