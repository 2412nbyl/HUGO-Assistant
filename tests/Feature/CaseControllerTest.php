<?php

namespace Tests\Feature;

use App\Models\NotarisCase;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * White-Box Feature Tests — CaseController
 *
 * Covers every internal branch of:
 *   - updateStatus() — role-permission guard, status validation, payment sync
 *   - updateNote()   — note save and timeline logging
 *   - store()        — sanitization, payment creation, redirect
 *   - destroy()      — deletion and redirect
 *
 * DB is reset each test via RefreshDatabase.
 */
class CaseControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function makeUser(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function makeCase(array $attrs = []): NotarisCase
    {
        return NotarisCase::factory()->create(array_merge(['status' => 'proses'], $attrs));
    }

    private function makePayment(NotarisCase $case, string $status = 'belum'): Payment
    {
        return Payment::factory()->create([
            'id_kasus' => $case->id_kasus,
            'status'   => $status,
        ]);
    }

    // ─── updateStatus ─────────────────────────────────────────────────────────

    /** @test — Path 1: Invalid status string → 422 */
    public function update_status_rejects_invalid_status_value(): void
    {
        $user = $this->makeUser('notaris');
        $case = $this->makeCase();

        $response = $this->actingAs($user)
            ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'invalid_status']);

        $response->assertStatus(422)
                 ->assertJsonPath('error', 'Status tidak valid.');
    }

    /** @test — Path 2: Freelancer tries to set 'selesai' → 403 */
    public function update_status_denies_freelancer_setting_selesai(): void
    {
        $user = $this->makeUser('freelancer');
        $case = $this->makeCase();

        $response = $this->actingAs($user)
            ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'selesai']);

        $response->assertStatus(403);
    }

    /** @test — Path 3: Freelancer sets 'proses' (allowed) → 200 */
    public function update_status_allows_freelancer_to_set_proses(): void
    {
        $user = $this->makeUser('freelancer');
        $case = $this->makeCase(['status' => 'tertunda']);

        $response = $this->actingAs($user)
            ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'proses']);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);
    }

    /** @test — Path 4: Status 'selesai' + payment 'belum' → payment auto-upgraded to 'lunas' */
    public function update_status_to_selesai_auto_upgrades_belum_payment_to_lunas(): void
    {
        $user    = $this->makeUser('notaris');
        $case    = $this->makeCase(['status' => 'proses']);
        $payment = $this->makePayment($case, 'belum');

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'selesai'])
             ->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id_transaksi' => $payment->id_transaksi,
            'status'       => 'lunas',
        ]);
        $this->assertDatabaseHas('payment_histories', [
            'payment_id' => $payment->id_transaksi,
            'from_status' => 'belum',
            'to_status'   => 'lunas',
        ]);
    }

    /** @test — Path 5: Status 'selesai' + payment already 'lunas' → no history entry */
    public function update_status_to_selesai_does_not_duplicate_payment_when_already_lunas(): void
    {
        $user    = $this->makeUser('notaris');
        $case    = $this->makeCase(['status' => 'proses']);
        $payment = $this->makePayment($case, 'lunas');

        $countBefore = PaymentHistory::count();

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'selesai']);

        $this->assertSame($countBefore, PaymentHistory::count());
    }

    /** @test — Path 6: Status 'proses' + payment 'lunas' → payment auto-reverted to 'belum' */
    public function update_status_to_proses_reverts_lunas_payment_to_belum(): void
    {
        $user    = $this->makeUser('notaris');
        $case    = $this->makeCase(['status' => 'selesai']);
        $payment = $this->makePayment($case, 'lunas');

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'proses'])
             ->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id_transaksi' => $payment->id_transaksi,
            'status'       => 'belum',
        ]);
    }

    /** @test — Path 7: Status 'tertunda' + payment 'lunas' → payment reverted to 'belum' */
    public function update_status_to_tertunda_reverts_lunas_payment_to_belum(): void
    {
        $user    = $this->makeUser('staff');
        $case    = $this->makeCase(['status' => 'selesai']);
        $payment = $this->makePayment($case, 'lunas');

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'tertunda'])
             ->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id_transaksi' => $payment->id_transaksi,
            'status'       => 'belum',
        ]);
    }

    /** @test — Path 8: No payment exists → no exception, still succeeds */
    public function update_status_succeeds_when_case_has_no_payment(): void
    {
        $user = $this->makeUser('notaris');
        $case = $this->makeCase();
        // Deliberately no Payment record created

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'selesai'])
             ->assertStatus(200)
             ->assertJsonPath('success', true);
    }

    /** @test — Path 9: Returns old and new status in response */
    public function update_status_response_includes_old_and_new_status(): void
    {
        $user = $this->makeUser('notaris');
        $case = $this->makeCase(['status' => 'proses']);

        $response = $this->actingAs($user)
            ->postJson("/cases/{$case->id_kasus}/status", ['status' => 'selesai']);

        $response->assertJsonPath('old', 'proses')
                 ->assertJsonPath('new', 'selesai');
    }

    // ─── updateNote ───────────────────────────────────────────────────────────

    /** @test — Note is saved and 200 returned */
    public function update_note_saves_progress_note_and_returns_success(): void
    {
        $user = $this->makeUser('staff');
        $case = $this->makeCase();

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/note", ['note' => 'Menunggu dokumen KK'])
             ->assertStatus(200)
             ->assertJsonPath('success', true);

        $this->assertDatabaseHas('cases', [
            'id_kasus'     => $case->id_kasus,
            'progress_note' => 'Menunggu dokumen KK',
        ]);
    }

    /** @test — Empty note is accepted (nullable) */
    public function update_note_accepts_empty_string(): void
    {
        $user = $this->makeUser('staff');
        $case = $this->makeCase(['progress_note' => 'old note']);

        $this->actingAs($user)
             ->postJson("/cases/{$case->id_kasus}/note", ['note' => ''])
             ->assertStatus(200);

        $this->assertDatabaseHas('cases', [
            'id_kasus'     => $case->id_kasus,
            'progress_note' => null,
        ]);
    }

    // ─── store ────────────────────────────────────────────────────────────────

    /** @test — Valid POST creates a case and a Payment record */
    public function store_creates_case_and_payment_record(): void
    {
        $user = $this->makeUser('notaris');

        $response = $this->actingAs($user)->post('/cases', [
            'client_name'   => 'Budi Santoso',
            'case_name'     => 'Pendirian PT Maju',
            'type'          => 'PT',
            'deadline'      => now()->addMonth()->format('Y-m-d'),
            'nominal_bayar' => '15.000.000',
        ]);

        $response->assertRedirect(route('cases.index'));

        $case = NotarisCase::where('client_name', 'Budi Santoso')->first();
        $this->assertNotNull($case);
        $this->assertEquals('proses', $case->status);

        $this->assertDatabaseHas('payments', [
            'id_kasus' => $case->id_kasus,
            'status'   => 'belum',
        ]);
    }

    /** @test — Rupiah formatting is stripped before persistence */
    public function store_strips_rupiah_formatting_from_nominal_bayar(): void
    {
        $user = $this->makeUser('notaris');

        $this->actingAs($user)->post('/cases', [
            'client_name'   => 'Test Klien',
            'case_name'     => 'Kasus Uji',
            'type'          => 'CV',
            'deadline'      => now()->addMonth()->format('Y-m-d'),
            'nominal_bayar' => 'Rp. 5.000.000',
        ]);

        $case = NotarisCase::where('client_name', 'Test Klien')->first();
        $this->assertEquals(5000000, $case->nominal_bayar);
    }

    /** @test — Missing required fields → redirect back with errors */
    public function store_fails_validation_when_required_fields_missing(): void
    {
        $user = $this->makeUser('notaris');

        $response = $this->actingAs($user)->post('/cases', []);

        $response->assertSessionHasErrors(['client_name', 'case_name', 'type', 'deadline']);
    }

    /** @test — Invalid type → validation fails */
    public function store_rejects_invalid_case_type(): void
    {
        $user = $this->makeUser('notaris');

        $response = $this->actingAs($user)->post('/cases', [
            'client_name' => 'X',
            'case_name'   => 'Y',
            'type'        => 'INVALID',
            'deadline'    => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['type']);
    }

    // ─── destroy ──────────────────────────────────────────────────────────────

    /** @test */
    public function destroy_deletes_case_and_redirects(): void
    {
        $user = $this->makeUser('notaris');
        $case = $this->makeCase(['client_name' => 'Klien Hapus']);

        $response = $this->actingAs($user)
            ->delete("/cases/{$case->id_kasus}");

        $response->assertRedirect(route('cases.index'));
        $this->assertDatabaseMissing('cases', ['id_kasus' => $case->id_kasus]);
    }

    /** @test */
    public function destroy_returns_404_for_nonexistent_case(): void
    {
        $user = $this->makeUser('notaris');

        $this->actingAs($user)
             ->delete('/cases/CS-NONEXISTENT')
             ->assertStatus(404);
    }

    /** @test */
    public function admin_is_blocked_from_cases(): void
    {
        $user = $this->makeUser('admin');

        // Middleware returns 403 for JSON requests; regular web requests redirect (302)
        $this->actingAs($user)
             ->deleteJson('/cases/CS-ANY')
             ->assertStatus(403);
    }
}
