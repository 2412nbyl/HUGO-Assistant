<?php

namespace App\Http\Controllers;

use App\Models\NotarisCase;
use Illuminate\Http\Request;

class BirthdayController extends Controller
{
    /**
     * Return clients whose birthday is today or within 5 days.
     * Used by the dashboard to show countdown badges / popup.
     */
    public function today(Request $request)
    {
        // (Removed automated chat ping. Birthdays are now UI-modal only)

        $today = now();
        $cases = NotarisCase::whereNotNull('birth_date')->get();

        $birthdays = $cases->filter(function ($case) {
            if (!$case->birth_date) return false;
            $days = $case->daysUntilBirthday();
            return $days !== null && $days <= 5;
        })->map(function ($case) {
            return [
                'id'          => $case->id,
                'client_name' => $case->client_name,
                'case_name'   => $case->case_name,
                'birth_date'  => $case->birth_date->format('d M'),
                'days_until'  => $case->daysUntilBirthday(),
                'is_today'    => $case->isBirthdayToday(),
            ];
        })->values();

        return response()->json($birthdays);
    }
}
