<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $guarded   = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * A roomuser belongs to an user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A roomuser belongs to a room.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

}
