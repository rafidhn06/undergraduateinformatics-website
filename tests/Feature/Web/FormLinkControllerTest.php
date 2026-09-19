<?php

namespace Tests\Feature\Web;

use App\Models\FeedbackLink;
use App\Models\MsFormDefinition;
use App\Models\ReservationLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\FakesMicrosoftForms;
use Tests\TestCase;

class FormLinkControllerTest extends TestCase
{
    use FakesMicrosoftForms;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        FeedbackLink::query()->delete();
        ReservationLink::query()->delete();
    }

    private function createAdminUser(): User
    {
        return User::create([
            'email' => fake()->unique()->safeEmail(),
            'password_recovery_id' => 1,
            'password' => 'password',
        ]);
    }

    public function test_form_link_page_requires_authentication(): void
    {
        $this->get('/admin/form-links')->assertRedirect(route('admin.login'));
    }

    public function test_form_link_page_renders_both_links(): void
    {
        FeedbackLink::create(['link' => 'https://example.com/forms']);
        ReservationLink::create(['link' => 'https://example.com/reservation']);

        $response = $this->actingAs($this->createAdminUser())->get('/admin/form-links');

        $response->assertStatus(200);
        $response->assertViewIs('AdminDashboard.feedback');
        $response->assertSee('Manajemen Form Link');
        $response->assertSee('https://example.com/forms');
        $response->assertSee('https://example.com/reservation');
    }

    public function test_form_link_update_changes_both_links(): void
    {
        Http::fake($this->microsoftEndpoints());
        $feedback = FeedbackLink::create(['link' => 'https://example.com/old']);
        $reservation = ReservationLink::create(['link' => 'https://example.com/old']);
        $this->actingAs($this->createAdminUser());

        $response = $this->put('/admin/form-links', [
            'feedback_link' => 'https://forms.office.com/r/abc123',
            'reservation_link' => 'https://forms.office.com/r/abc123',
        ]);

        $response->assertRedirect(route('admin.form-links.show'));
        $response->assertSessionHas('success');
        $this->assertSame('https://forms.office.com/r/abc123', $feedback->fresh()->link);
        $this->assertSame('https://forms.office.com/r/abc123', $reservation->fresh()->link);
    }

    public function test_form_link_update_rejects_invalid_url(): void
    {
        $link = FeedbackLink::create(['link' => 'https://example.com/old']);
        $this->actingAs($this->createAdminUser());

        $response = $this->put('/admin/form-links', [
            'feedback_link' => 'not-a-url',
        ]);

        $response->assertSessionHasErrors('feedback_link');
        $this->assertSame('https://example.com/old', $link->fresh()->link);
    }

    public function test_feedback_update_triggers_a_definition_refresh(): void
    {
        Http::fake($this->microsoftEndpoints());
        FeedbackLink::create(['link' => 'https://forms.office.com/r/abc123']);
        $this->actingAs($this->createAdminUser());

        $response = $this->put('/admin/form-links', [
            'feedback_link' => 'https://forms.office.com/r/abc123',
        ]);

        $response->assertRedirect(route('admin.form-links.show'));
        $response->assertSessionHas('success');
        $this->assertNotNull(MsFormDefinition::query()->where('kind', 'feedback')->first()?->payload);
    }

    public function test_reservation_update_keeps_new_link_and_warns_when_refresh_fails(): void
    {
        Http::fake(['https://forms.office.com/r/*' => Http::response('', 500)]);
        MsFormDefinition::query()->create([
            'kind' => 'reservation',
            'link' => 'https://forms.office.com/r/abc123',
            'payload' => ['link' => 'https://forms.office.com/r/abc123', 'title' => ['text' => 'old']],
            'fetched_at' => now()->subDay(),
        ]);
        $this->actingAs($this->createAdminUser());

        $response = $this->put('/admin/form-links', [
            'reservation_link' => 'https://forms.office.com/r/abc123',
        ]);

        $response->assertRedirect(route('admin.form-links.show'));
        $response->assertSessionHas('warning');
        $this->assertSame('https://forms.office.com/r/abc123', ReservationLink::query()->firstOrFail()->link);
        $this->assertSame('old', MsFormDefinition::query()->where('kind', 'reservation')->firstOrFail()->payload['title']['text']);
    }
}
