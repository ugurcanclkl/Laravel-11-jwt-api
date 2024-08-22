<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Room extends Model
{
    use HasFactory;

    protected $guarded   = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * A room can have many roomuser.
     */
    public function roomusers(): HasMany
    {
        return $this->HasMany(RoomUser::class);
    }

    /**
     * A room can have many users through roomuser.
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, RoomUser::class, 'room_id', 'id', 'id', 'user_id');
    }

    /**
     * A room can have many messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
