<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAuditLog extends Model
{
    protected $table = 'payment_audit_logs';
    
    protected $fillable = [
        'admin_id',
        'auditable_type',
        'auditable_id',
        'payment_id',
        'withdrawal_request_id',
        'transaction_id',
        'action',
        'status_before',
        'status_after',
        'amount',
        'reason',
        'ip_address',
        'user_agent',
        'metadata',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];
    
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
    
    public function withdrawalRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }
    
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
    
    public function auditable()
    {
        return $this->morphTo();
    }
}