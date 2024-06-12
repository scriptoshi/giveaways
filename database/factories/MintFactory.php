<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MintFactory extends Factory
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
			'owner' => $this->faker->words(2,true),
			'nft_contract' => $this->faker->words(2,true),
			'nft' => $this->faker->words(2,true),
			'tokenId' => $this->faker->words(2,true),
			'txhash' => $this->faker->words(2,true),
			'verified' => $this->faker->boolean(),

        ];
    }



}
