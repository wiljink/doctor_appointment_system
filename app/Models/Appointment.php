<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'appointment_number',
        'patient_id',
        'doctor_id',
        'department_id',
        'appointment_date',
        'appointment_time',
        'end_time',
        'status',
        'reason',
        'notes',
        'fee',
        'payment_status',
        'payment_method',
        'transaction_id',
        'paid_at',
        'reminder_sent_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'paid_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'fee' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * @return BelongsTo<Doctor, $this>
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return HasOne<MedicalRecord, $this>
     */
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'], true);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'completed' => 'bg-blue-100 text-blue-800 border-blue-300',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
            'no_show' => 'bg-zinc-100 text-zinc-800 border-zinc-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
