<?php

namespace App\Console\Commands;

use App\Models\Shelf;
use App\Models\Book;
use Illuminate\Console\Command;

class SyncShelfCounts extends Command
{
    protected $signature = 'shelves:sync';
    protected $description = 'Sync shelf current_count with actual book counts';

    public function handle()
    {
        $shelves = Shelf::all();
        $count = 0;

        foreach ($shelves as $shelf) {
            $bookCount = Book::where('shelf_number', $shelf->code)
                ->whereIn('status', ['approved', 'active'])
                ->count();

            $shelf->current_count = $bookCount;
            $shelf->save();
            $count++;
        }

        $this->info("Synced {$count} shelves.");
        return Command::SUCCESS;
    }
}