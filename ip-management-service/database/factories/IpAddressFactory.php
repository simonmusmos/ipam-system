<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\IpAddress;

class IpAddressFactory extends Factory
{
    protected $model = IpAddress::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'address' => $this->faker->ipv4,
            'label' => $this->faker->word,
            'comment' => $this->faker->sentence,
        ];
    }
}