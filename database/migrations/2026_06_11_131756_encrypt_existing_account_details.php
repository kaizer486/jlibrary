<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\WithdrawalRequest;

return new class extends Migration
{
    public function up(): void
    {
        // Encrypt all existing unencrypted account_details
        $withdrawals = WithdrawalRequest::whereNotNull('account_details')->get();
        
        foreach ($withdrawals as $withdrawal) {
            $details = $withdrawal->getOriginal('account_details');
            
            // Check if already encrypted (looks like base64)
            if (!preg_match('/^eyJpdiI6/', $details)) {
                $withdrawal->account_details = $details;
                $withdrawal->saveQuietly(); // Trigger the accessor
            }
        }
    }

    public function down(): void
    {
        
    }
};