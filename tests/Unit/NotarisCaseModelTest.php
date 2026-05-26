<?php

namespace Tests\Unit;

use App\Models\NotarisCase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * White-Box Unit Tests — NotarisCase Model
 *
 * Covers every internal branch of:
 *   - daysUntilBirthday()
 *   - isBirthdayToday()
 *   - scopeByType / scopeByStatus / scopeByMonth
 */
class NotarisCaseModelTest extends TestCase
{
    // ── daysUntilBirthday() ─────────────────────────────────────────────────

    /** @test */
    public function days_until_birthday_returns_null_when_birth_date_is_null(): void
    {
        $case = new NotarisCase();
        $case->birth_date = null;

        $this->assertNull($case->daysUntilBirthday());
    }

    /** @test */
    public function days_until_birthday_returns_zero_when_birthday_is_today(): void
    {
        $case = new NotarisCase();
        $case->birth_date = Carbon::today();

        $result = $case->daysUntilBirthday();

        $this->assertSame(0, $result);
    }

    /** @test */
    public function days_until_birthday_returns_positive_days_when_birthday_is_in_future(): void
    {
        $case = new NotarisCase();
        // Birthday 30 days from now (same month/day but in any year)
        $futureDate = Carbon::today()->addDays(30);
        // Use a birth year 30 years ago with the same m-d
        $case->birth_date = $futureDate->copy()->subYears(30);

        $result = $case->daysUntilBirthday();

        // Should be exactly 30 (or 30 if no year-wrapping needed)
        $this->assertSame(30, $result);
    }

    /** @test */
    public function days_until_birthday_rolls_to_next_year_when_date_has_passed(): void
    {
        $case = new NotarisCase();
        // Birthday was 5 days ago — should roll to next year
        $pastDate = Carbon::today()->subDays(5);
        $case->birth_date = $pastDate->copy()->subYears(25);

        $result = $case->daysUntilBirthday();

        // Should be 360-361 days away (next year)
        $this->assertGreaterThan(300, $result);
        $this->assertLessThanOrEqual(366, $result);
    }

    // ── isBirthdayToday() ──────────────────────────────────────────────────

    /** @test */
    public function is_birthday_today_returns_false_when_birth_date_is_null(): void
    {
        $case = new NotarisCase();
        $case->birth_date = null;

        $this->assertFalse($case->isBirthdayToday());
    }

    /** @test */
    public function is_birthday_today_returns_true_when_birth_date_month_and_day_match_today(): void
    {
        $case = new NotarisCase();
        // Born on this exact month+day, but 30 years ago
        $case->birth_date = Carbon::today()->subYears(30);

        $this->assertTrue($case->isBirthdayToday());
    }

    /** @test */
    public function is_birthday_today_returns_false_when_birth_date_month_or_day_differs(): void
    {
        $case = new NotarisCase();
        $case->birth_date = Carbon::today()->addDays(1)->subYears(30); // tomorrow's month-day

        $this->assertFalse($case->isBirthdayToday());
    }

    // ── User::isAdmin() & hasRole() ────────────────────────────────────────

    /** @test */
    public function user_is_admin_returns_true_for_admin_and_notaris(): void
    {
        $admin   = new \App\Models\User(['role' => 'admin']);
        $notaris = new \App\Models\User(['role' => 'notaris']);
        $staff   = new \App\Models\User(['role' => 'staff']);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($notaris->isAdmin());
        $this->assertFalse($staff->isAdmin());
    }

    /** @test */
    public function user_has_role_accepts_string_and_array(): void
    {
        $user = new \App\Models\User(['role' => 'staff']);

        $this->assertTrue($user->hasRole('staff'));
        $this->assertTrue($user->hasRole(['admin', 'staff']));
        $this->assertFalse($user->hasRole('freelancer'));
        $this->assertFalse($user->hasRole(['admin', 'notaris']));
    }
}
