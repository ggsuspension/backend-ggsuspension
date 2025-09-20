<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceQueue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_queues';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_profile_id',
        'customer_motor_id',
        'service_type_id',
        'services',
        'checked_in_at',
        'completed_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'services' => 'array',
        'checked_in_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the customer profile that owns the queue.
     */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    /**
     * Get the customer motor that owns the queue.
     */
    public function customerMotor(): BelongsTo
    {
        return $this->belongsTo(CustomerMotor::class);
    }
}
