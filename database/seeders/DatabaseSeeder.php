<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Train;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        User::create([
            'name' => 'Station Admin',
            'email' => 'admin@station.test',
            'phone' => '+201000000000',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $passengers = collect([
            ['Omar Hassan', 'omar@example.test', '+201001111111'],
            ['Sara Ali', 'sara@example.test', '+201002222222'],
            ['Youssef Adel', 'youssef@example.test', '+201003333333'],
            ['Mona Farid', 'mona@example.test', '+201004444444'],
        ])->map(fn ($p) => User::create([
            'name' => $p[0],
            'email' => $p[1],
            'phone' => $p[2],
            'role' => 'passenger',
            'password' => Hash::make('password'),
        ]));

        $trains = collect([
            ['Cairo Express', 'CX-101', 300],
            ['Delta Line', 'DL-202', 250],
            ['Upper Egypt Star', 'UE-303', 200],
        ])->map(fn ($t) => Train::create([
            'name' => $t[0],
            'code' => $t[1],
            'total_seats' => $t[2],
        ]));

        $base = Carbon::now()->startOfHour();
        $trips = collect([
            [0, 'Cairo', 'Alexandria', 2, 2, 200],
            [0, 'Cairo', 'Aswan', 5, 12, 880],
            [1, 'Cairo', 'Mansoura', 3, 2, 130],
            [2, 'Cairo', 'Luxor', 6, 9, 650],
        ])->map(function ($t) use ($trains, $base) {
            $train = $trains[$t[0]];
            $depart = $base->copy()->addHours($t[3]);

            return Trip::create([
                'train_id' => $train->id,
                'origin' => $t[1],
                'destination' => $t[2],
                'departure_time' => $depart,
                'arrival_time' => $depart->copy()->addHours($t[4]),
                'route_distance_km' => $t[5],
                'status' => 'on_time',
                'available_seats' => $train->total_seats,
            ]);
        });

        foreach ($trips as $i => $trip) {
            foreach ($passengers as $j => $passenger) {
                if (($i + $j) % 2 === 0) {
                    $seats = 1 + ($j % 2);
                    Booking::create([
                        'user_id' => $passenger->id,
                        'trip_id' => $trip->id,
                        'reference' => Booking::generateReference(),
                        'seats' => $seats,
                        'status' => 'confirmed',
                    ]);
                    $trip->decrement('available_seats', $seats);
                }
            }
        }
    }
}
