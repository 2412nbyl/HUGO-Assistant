<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\NotarisCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'id_kasus' => NotarisCase::factory(),
            'amount'   => 'Rp. ' . number_format($this->faker->numberBetween(1_000_000, 50_000_000), 0, ',', '.'),
            'status'   => 'belum',
        ];
    }

    public function lunas(): static
    {
        return $this->state(['status' => 'lunas']);
    }

    public function sebagian(): static
    {
        return $this->state(['status' => 'sebagian']);
    }
}
