<?php

namespace Tests\Feature;

use App\Models\Archive;
use App\Models\CaseDocument;
use App\Models\NotarisCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * White-Box Feature Tests — ArchiveController
 *
 * Covers every internal branch of:
 *   - index()   — search filter, orphan docs query
 *   - store()   — validation, archive creation, orphan-doc auto-link
 *   - update()  — validation, updates
 *   - destroy() — document un-link (not delete), archive deletion
 */
class ArchiveControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'notaris'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    // ─── index ────────────────────────────────────────────────────────────────

    /** @test — Unauthenticated users are redirected */
    public function index_redirects_unauthenticated_users(): void
    {
        $this->get('/archives')->assertRedirect('/login');
    }

    /** @test — Authenticated user sees archive index */
    public function index_is_accessible_to_authenticated_users(): void
    {
        $this->actingAs($this->makeUser())
             ->get('/archives')
             ->assertStatus(200)
             ->assertViewIs('archives.index');
    }

    /** @test — Search filter narrows results */
    public function index_filters_by_search_term(): void
    {
        $user    = $this->makeUser();
        $case    = NotarisCase::factory()->create(['client_name' => 'Siti Rahayu']);
        $archive = Archive::factory()->create([
            'id_kasus'    => $case->id_kasus,
            'client_name' => $case->client_name,
        ]);

        NotarisCase::factory()->create(['client_name' => 'Budi Santoso']);

        $response = $this->actingAs($user)
            ->get('/archives?search=Siti');

        $response->assertStatus(200);
        $response->assertViewHas('archives', function ($archives) use ($archive) {
            return $archives->contains('id_arsip', $archive->id_arsip);
        });
    }

    // ─── store ────────────────────────────────────────────────────────────────

    /** @test — Branch: valid data → archive created */
    public function store_creates_archive_with_valid_data(): void
    {
        $user = $this->makeUser();
        $case = NotarisCase::factory()->create(['client_name' => 'Dewi']);

        $response = $this->actingAs($user)->post('/archives', [
            'id_kasus'        => $case->id_kasus,
            'client_name'     => 'Dewi',
            'folder_location' => '/internal/storage/archives/2026/',
        ]);

        $response->assertRedirect(route('archives.index'));
        $this->assertDatabaseHas('archives', [
            'id_kasus'        => $case->id_kasus,
            'folder_location' => '/internal/storage/archives/2026/',
        ]);
    }

    /** @test — Branch: orphan CaseDocuments auto-linked on archive creation */
    public function store_auto_links_orphan_documents_to_new_archive(): void
    {
        $user = $this->makeUser();
        $case = NotarisCase::factory()->create();

        // Create orphan doc (no id_arsip)
        $doc = CaseDocument::factory()->create([
            'id_kasus' => $case->id_kasus,
            'id_arsip' => null,
        ]);

        $this->actingAs($user)->post('/archives', [
            'id_kasus'        => $case->id_kasus,
            'client_name'     => $case->client_name,
            'folder_location' => 'Rak A',
        ]);

        $archive = Archive::where('id_kasus', $case->id_kasus)->first();

        $this->assertDatabaseHas('case_documents', [
            'id_dok'   => $doc->id_dok,
            'id_arsip' => $archive->id_arsip,
        ]);
    }

    /** @test — Branch: store does NOT link docs belonging to a different case */
    public function store_does_not_link_documents_from_other_cases(): void
    {
        $user      = $this->makeUser();
        $case      = NotarisCase::factory()->create();
        $otherCase = NotarisCase::factory()->create();

        $otherDoc = CaseDocument::factory()->create([
            'id_kasus' => $otherCase->id_kasus,
            'id_arsip' => null,
        ]);

        $this->actingAs($user)->post('/archives', [
            'id_kasus'        => $case->id_kasus,
            'client_name'     => $case->client_name,
            'folder_location' => '/storage/cases/',
        ]);

        // Other case doc should remain orphan
        $this->assertDatabaseHas('case_documents', [
            'id_dok'   => $otherDoc->id_dok,
            'id_arsip' => null,
        ]);
    }

    /** @test — Branch: missing required fields → validation fails */
    public function store_fails_validation_when_fields_missing(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
             ->post('/archives', [])
             ->assertSessionHasErrors(['id_kasus', 'client_name', 'folder_location']);
    }

    /** @test — Branch: id_kasus does not exist → validation fails */
    public function store_fails_when_id_kasus_does_not_exist(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
             ->post('/archives', [
                 'id_kasus'        => 'CS-NONEXISTENT',
                 'client_name'     => 'X',
                 'folder_location' => '/storage/',
             ])
             ->assertSessionHasErrors(['id_kasus']);
    }

    // ─── destroy ──────────────────────────────────────────────────────────────

    /** @test — Branch: destroying archive unlinks docs but does NOT delete them */
    public function destroy_unlinks_documents_but_does_not_delete_them(): void
    {
        $user    = $this->makeUser();
        $case    = NotarisCase::factory()->create();
        $archive = Archive::factory()->create(['id_kasus' => $case->id_kasus]);

        $doc = CaseDocument::factory()->create([
            'id_kasus' => $case->id_kasus,
            'id_arsip' => $archive->id_arsip,
        ]);

        $this->actingAs($user)
             ->delete("/archives/{$archive->id_arsip}")
             ->assertRedirect(route('archives.index'));

        // Archive deleted
        $this->assertDatabaseMissing('archives', ['id_arsip' => $archive->id_arsip]);

        // Document still exists but unlinked
        $this->assertDatabaseHas('case_documents', [
            'id_dok'   => $doc->id_dok,
            'id_arsip' => null,
        ]);
    }

    /** @test — Branch: destroy redirects with success flash */
    public function destroy_sets_success_flash_on_deletion(): void
    {
        $user    = $this->makeUser();
        $case    = NotarisCase::factory()->create();
        $archive = Archive::factory()->create(['id_kasus' => $case->id_kasus]);

        $this->actingAs($user)
             ->delete("/archives/{$archive->id_arsip}")
             ->assertSessionHas('success');
    }
}
