<?php

namespace Database\Seeders;

use App\Models\Concerns\RoomUserStatus;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {

            $user = User::firstWhere([
                'email' => 'user@mail.com',
            ]);

            RoomUser::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'status'  => RoomUserStatus::ACCEPTED,
            ]);
        }
    }
}
