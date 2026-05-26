<?php

namespace Database\Factories;

use App\Models\Archive;
use App\Models\NotarisCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchiveFactory extends Factory
{
    protected $model = Archive::class;

    public function definition(): array
    {
        return [
            'id_kasus'        => NotarisCase::factory(),
            'id_klien'        => null,
            'client_name'     => $this->faker->name(),
            'folder_location' => $this->faker->randomElement([
                '/internal/storage/archives/2026/',
                'Rak Utama, Map Biru A',
                'Lemari Arsip Utama (Main Cabinet)',
            ]),
        ];
    }
}
