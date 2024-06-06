<?php

namespace Database\Factories;
use App\Models\Giveaway;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopupFactory extends Factory
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
			'paid' => $this->faker->randomFloat(4),
			'paid_before' => $this->faker->randomFloat(4),
			'prize_before' => $this->faker->randomFloat(4),
			'fee_before' => $this->faker->randomFloat(4),
			'sleep_before' => $this->faker->randomFloat(16),
			'hash' => $this->faker->words(2,true),
			'num_winners' => $this->faker->randomNumber(5,false),
			'num_winners_before' => $this->faker->randomNumber(5,false),
			'status' => $this->faker->words(2,true),

        ];
    }



}
