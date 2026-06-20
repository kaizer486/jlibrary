<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic access for individual readers',
                'price' => 0,
                'currency' => 'TZS',
                'billing_interval' => 'monthly',
                'features' => [
                    'max_books' => 5,
                    'download_pdf' => false,
                    'ai_assistant' => false,
                    'analytics' => false,
                ],
                'max_users' => 1,
                'max_books' => 5,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'For avid readers and learners',
                'price' => 5000,
                'currency' => 'TZS',
                'billing_interval' => 'monthly',
                'features' => [
                    'max_books' => 50,
                    'download_pdf' => true,
                    'ai_assistant' => true,
                    'analytics' => false,
                ],
                'max_users' => 1,
                'max_books' => 50,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'For power users and professionals',
                'price' => 15000,
                'currency' => 'TZS',
                'billing_interval' => 'monthly',
                'features' => [
                    'max_books' => 500,
                    'download_pdf' => true,
                    'ai_assistant' => true,
                    'analytics' => true,
                ],
                'max_users' => 5,
                'max_books' => 500,
                'sort_order' => 3,
            ],
            [
                'name' => 'Institution',
                'slug' => 'institution',
                'description' => 'For schools, colleges, and organizations',
                'price' => 100000,
                'currency' => 'TZS',
                'billing_interval' => 'monthly',
                'features' => [
                    'max_books' => 5000,
                    'download_pdf' => true,
                    'ai_assistant' => true,
                    'analytics' => true,
                    'multi_user' => true,
                ],
                'max_users' => 1000,
                'max_books' => 5000,
                'sort_order' => 4,
            ],
        ];
        
        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}