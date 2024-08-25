<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Player::create([
            'name' => 'Fulano de tal 1',
            'level' => 3,
            'is_goalkeeper' => false,
            'is_present' => true,
        ]);

        Player::create([
            'name' => 'Fulano de tal 2',
            'level' => 4,
            'is_goalkeeper' => true,
            'is_present' => false,
        ]);

        Player::create([
            'name' => 'Fulano de tal 3',
            'level' => 2,
            'is_goalkeeper' => false,
            'is_present' => true,
        ]);

        for ($i = 4; $i <= 10; $i++) {
            Player::create([
                'name' => 'Fulano de tal ' . $i,
                'level' => rand(1, 5),
                'is_goalkeeper' => rand(0, 1),
                'is_present' => rand(0, 1),
            ]);
        }
    }
}
