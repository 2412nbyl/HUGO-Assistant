<?php

namespace Database\Factories;

use App\Models\CaseDocument;
use App\Models\NotarisCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseDocumentFactory extends Factory
{
    protected $model = CaseDocument::class;

    public function definition(): array
    {
        return [
            'id_kasus'    => NotarisCase::factory(),
            'id_klien'    => null,
            'id_arsip'    => null,
            'filename'    => $this->faker->word() . '.pdf',
            'filepath'    => 'case-documents/' . $this->faker->uuid() . '.pdf',
            'uploaded_by' => null,
        ];
    }
}
