<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class CentralPaymentNotification extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'tenant_id',
        'client_email',
        'message',
        'attachment_path',
        'status',
        'reviewed_at'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
