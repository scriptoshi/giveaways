<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
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
			'user_id' => User::factory(),
			'uuid' => $this->faker->uuid(),
			'category' => $this->faker->randomElement(['shillers','attendee','doxxer','auditor','developer','listing','host','moderator','articles','influencer','other']),
			'tags' => $this->fakeJson(),
			'title' => $this->faker->words(2,true),
			'description' => $this->faker->sentence(50),
			'price' => $this->faker->randomFloat(2),
			'duration' => $this->faker->randomNumber(5,false),
			'period' => $this->faker->randomElement(['hours','days','weeks','months']),
			'rating' => $this->faker->randomNumber(5,false),
			'telegram' => $this->faker->words(2,true),
			'url' => $this->faker->words(2,true),
			'verified_at' => $this->faker->unixTime(),
			'promoted_at' => $this->faker->unixTime(),

        ];
    }


 	public  function fakeJson()
	{
		return json_encode([
			'number' => $this->faker->randomNumber(5, false),
			'date' => now(),
		]);
	}
}
