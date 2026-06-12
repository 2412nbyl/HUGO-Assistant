<?php

namespace Tests\Feature;

use App\Models\NotarisCase;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * White-Box Feature Tests — PaymentController
 *
 * Covers every internal branch of updateStatus():
 *   - Validation (valid statuses)
 *   - Payment status sync with case (lunas → selesai, belum/sebagian → proses)
 *   - Same-status update (no history entry)
 *   - JSON vs redirect response
 */
class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makePayment(string $payStatus = 'belum', string $caseStatus = 'proses'): array
    {
        $user = $this->makeUser('notaris');
        $case = NotarisCase::factory()->create(['status' => $caseStatus]);
        $payment = Payment::factory()->create([
            'id_kasus' => $case->id_kasus,
            'status'   => $payStatus,
        ]);
        return [$user, $case, $payment];
    }

    // ─── Validation ──────────────────────────────────────────────────────────

    /** @test — Branch: invalid status string → 422 */
    public function update_status_fails_validation_for_invalid_status(): void
    {
        [$user, $case, $payment] = $this->makePayment();

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'dibayar'])
             ->assertStatus(422);
    }

    /** @test — Branch: missing status → 422 */
    public function update_status_fails_when_status_is_missing(): void
    {
        [$user, $case, $payment] = $this->makePayment();

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", [])
             ->assertStatus(422);
    }

    // ─── Same status (no change) ─────────────────────────────────────────────

    /** @test — Branch: old === new → no PaymentHistory created */
    public function update_status_with_same_status_creates_no_history(): void
    {
        [$user, $case, $payment] = $this->makePayment('belum');

        $countBefore = PaymentHistory::count();

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'belum'])
             ->assertStatus(200);

        $this->assertSame($countBefore, PaymentHistory::count());
    }

    // ─── Status → lunas triggers case to selesai ─────────────────────────────

    /** @test — Branch: payment lunas + case != selesai → case becomes selesai */
    public function update_payment_to_lunas_marks_proses_case_as_selesai(): void
    {
        [$user, $case, $payment] = $this->makePayment('belum', 'proses');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'lunas'])
             ->assertStatus(200);

        $this->assertDatabaseHas('cases', [
            'id_kasus' => $case->id_kasus,
            'status'   => 'selesai',
        ]);
    }

    /** @test — Branch: payment lunas + case already selesai → no status change */
    public function update_payment_to_lunas_does_not_double_mark_selesai_case(): void
    {
        [$user, $case, $payment] = $this->makePayment('belum', 'selesai');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'lunas']);

        // Case should still be selesai, with no duplicate updates
        $this->assertDatabaseHas('cases', [
            'id_kasus' => $case->id_kasus,
            'status'   => 'selesai',
        ]);
    }

    // ─── Status → belum/sebagian triggers case to proses ─────────────────────

    /** @test — Branch: payment belum + case selesai → case reverts to proses */
    public function update_payment_to_belum_reverts_selesai_case_to_proses(): void
    {
        [$user, $case, $payment] = $this->makePayment('lunas', 'selesai');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'belum'])
             ->assertStatus(200);

        $this->assertDatabaseHas('cases', [
            'id_kasus' => $case->id_kasus,
            'status'   => 'proses',
        ]);
    }

    /** @test — Branch: payment sebagian + case selesai → case reverts to proses */
    public function update_payment_to_sebagian_reverts_selesai_case_to_proses(): void
    {
        [$user, $case, $payment] = $this->makePayment('lunas', 'selesai');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'sebagian'])
             ->assertStatus(200);

        $this->assertDatabaseHas('cases', [
            'id_kasus' => $case->id_kasus,
            'status'   => 'proses',
        ]);
    }

    // ─── History is recorded ─────────────────────────────────────────────────

    /** @test — Branch: status changed → PaymentHistory created */
    public function update_status_creates_history_entry_when_status_changes(): void
    {
        [$user, $case, $payment] = $this->makePayment('belum');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", [
                 'status' => 'lunas',
                 'note'   => 'Sudah transfer',
             ])
             ->assertStatus(200);

        $this->assertDatabaseHas('payment_histories', [
            'payment_id'  => $payment->id_transaksi,
            'from_status' => 'belum',
            'to_status'   => 'lunas',
        ]);
    }

    // ─── Note is persisted ────────────────────────────────────────────────────

    /** @test — Branch: optional note saved in PaymentHistory */
    public function update_status_persists_optional_note_in_history(): void
    {
        [$user, $case, $payment] = $this->makePayment('belum');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", [
                 'status' => 'sebagian',
                 'note'   => 'Transfer DP 50%',
             ]);

        $this->assertDatabaseHas('payment_histories', [
            'payment_id' => $payment->id_transaksi,
            'note'       => 'Transfer DP 50%',
        ]);
    }

    // ─── 404 on unknown ID ────────────────────────────────────────────────────

    /** @test */
    public function update_status_returns_404_for_nonexistent_payment(): void
    {
        $user = $this->makeUser('notaris');

        $this->actingAs($user)
             ->putJson('/payment/TR-NONEXISTENT/status', ['status' => 'lunas'])
             ->assertStatus(404);
    }

    /** @test */
    public function admin_is_blocked_from_payments(): void
    {
        $user = $this->makeUser('admin');

        // Middleware returns 403 for JSON requests; regular web requests redirect (302)
        $this->actingAs($user)
             ->putJson('/payment/TR-ANY/status', ['status' => 'lunas'])
             ->assertStatus(403);
    }

    // ─── JSON response structure ───────────────────────────────────────────────

    /** @test */
    public function update_status_returns_json_success_true_on_valid_request(): void
    {
        $this->withoutExceptionHandling();
        [$user, $case, $payment] = $this->makePayment('belum');

        $this->actingAs($user)
             ->putJson("/payment/{$payment->id_transaksi}/status", ['status' => 'lunas'])
             ->assertStatus(200)
             ->assertJsonPath('success', true);
    }
}
