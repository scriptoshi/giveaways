<?php

namespace Database\Factories;
use App\Models\Giveaway;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
			'giveaway_id' => Giveaway::factory(),
			'user_id' => User::factory(),
			'username' => $this->faker->words(2,true),
			'groupId' => $this->faker->words(2,true),
			'type' => $this->faker->randomElement(['twitter','telegram','discord','tweet','mint','swap','contribute','api']),
			'status' => $this->faker->randomElement(['pending','draft','active']),
			'min' => $this->faker->randomFloat(8),
			'sleep' => $this->faker->randomNumber(5,false),

        ];
    }



}
