<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    protected $fillable = [
        'reference', 'customer_id', 'technician_id', 'service_type', 'unit_brand',
        'unit_model', 'unit_location', 'service_address', 'issue_description',
        'preferred_at', 'scheduled_at', 'status', 'priority', 'staff_notes',
    ];

    protected function casts(): array
    {
        return ['preferred_at' => 'datetime', 'scheduled_at' => 'datetime'];
    }

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, 'technician_id'); }
    public function record(): HasOne { return $this->hasOne(ServiceRecord::class); }
}
