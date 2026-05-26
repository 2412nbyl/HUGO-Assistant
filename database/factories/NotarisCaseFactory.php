<?php

namespace Database\Factories;

use App\Models\NotarisCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotarisCaseFactory extends Factory
{
    protected $model = NotarisCase::class;

    public function definition(): array
    {
        return [
            'client_name'   => $this->faker->name(),
            'case_name'     => $this->faker->sentence(3),
            'type'          => $this->faker->randomElement(['PT', 'CV', 'Pribadi']),
            'status'        => 'proses',
            'deadline'      => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'nominal_bayar' => $this->faker->numberBetween(1_000_000, 50_000_000),
            'phone'         => $this->faker->numerify('08##########'),
            'address'       => $this->faker->address(),
            'created_by'    => null,
        ];
    }
}
