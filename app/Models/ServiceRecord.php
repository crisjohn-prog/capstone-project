<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRecord extends Model
{
    protected $fillable = ['service_request_id', 'technician_id', 'diagnosis', 'work_performed', 'parts_used', 'recommendations', 'completed_at'];

    protected function casts(): array { return ['completed_at' => 'datetime']; }

    public function serviceRequest(): BelongsTo { return $this->belongsTo(ServiceRequest::class); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, 'technician_id'); }
}
