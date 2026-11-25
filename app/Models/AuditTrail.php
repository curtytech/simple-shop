<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditTrail extends Model
{
    protected $table = 'audit_trail';

    public $timestamps = false;

    protected $fillable = [
        'action',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(Model $actor, string $action, Model $auditable, string $ipAddress): self
    {
        $entry = new self([
            'action' => $action,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);

        $entry->actor()->associate($actor);
        $entry->auditable()->associate($auditable);
        $entry->save();

        return $entry;
    }
}
