<?php

namespace App\Models;

use App\Traits\Sendable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory, Sendable;

    protected $dates = ['starts_at', 'ends_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        return
            $this->registration_id &&
            $this->pool_id &&
            $this->offer_id &&
            $this->starts_at &&
            $this->ends_at;
    }

    public function updateFromLocation(array $location)
    {
        $this->registration_id = $location['inschrijvingId'];
        $this->offer_id = $location['laanbodId'];
        $this->ends_at = $location['eind'];
        $this->pool_id = $location['poolId'];
        $this->save();
    }

    function toFormBody(): array
    {
        return [
            'inschrijvingId' => (string) $this->registration_id,
            'poolId' => (string) $this->pool_id,
            'laanbodId' => (string) $this->offer_id,
            'start' => (string) $this->starts_at,
            'eind' => (string) $this->ends_at,
        ];
    }
}
