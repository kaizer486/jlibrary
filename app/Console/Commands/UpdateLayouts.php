<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateLayouts extends Command
{
    protected $signature = 'layouts:update';
    protected $description = 'Update all admin views to use master layout';

    public function handle()
    {
        $views = [
            // Admin Books
            resource_path('views/admin/books/index.blade.php'),
            resource_path('views/admin/books/create.blade.php'),
            resource_path('views/admin/books/edit.blade.php'),
            resource_path('views/admin/books/show.blade.php'),
            
            // Admin Users
            resource_path('views/admin/users/index.blade.php'),
            resource_path('views/admin/users/create.blade.php'),
            resource_path('views/admin/users/edit.blade.php'),
            resource_path('views/admin/users/show.blade.php'),
            
            // Admin Institutions
            resource_path('views/admin/institutions/index.blade.php'),
            resource_path('views/admin/institutions/create.blade.php'),
            resource_path('views/admin/institutions/edit.blade.php'),
            resource_path('views/admin/institutions/show.blade.php'),
            
            // Admin Marketplace
            resource_path('views/admin/marketplace/pending.blade.php'),
            
            // Admin Payments
            resource_path('views/admin/payments/index.blade.php'),
            resource_path('views/admin/payments/show.blade.php'),
            
            // Admin Other
            resource_path('views/admin/analytics.blade.php'),
            resource_path('views/admin/dashboard.blade.php'),
            
            // Super Admin
            resource_path('views/super-admin/dashboard.blade.php'),
        ];
        
        foreach ($views as $view) {
            if (!File::exists($view)) {
                $this->warn("File not found: " . basename($view));
                continue;
            }
            
            $content = File::get($view);
            
            // Replace @extends('layouts.admin') or @extends('layouts.super-admin')
            if (str_contains($content, "@extends('layouts.admin')")) {
                $content = str_replace(
                    "@extends('layouts.admin')",
                    "@extends('layouts.master')\n\n@section('page-content')",
                    $content
                );
                $this->info("Updated: " . basename($view) . " (was admin)");
            }
            
            if (str_contains($content, "@extends('layouts.super-admin')")) {
                $content = str_replace(
                    "@extends('layouts.super-admin')",
                    "@extends('layouts.master')\n\n@section('page-content')",
                    $content
                );
                $this->info("Updated: " . basename($view) . " (was super-admin)");
            }
            
            File::put($view, $content);
        }
        
        $this->info("\n✅ All layouts updated successfully!");
        $this->warn("⚠️  Make sure each view has @endsection at the end.");
    }
}