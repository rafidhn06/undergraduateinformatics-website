<?php

namespace Tests\Feature;

use App\Models\PasswordRecovery;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(string $email = 'admin@example.com'): User
    {
        $user = User::create([
            'email' => $email,
            'password' => 'oldpassword',
            'password_recovery_id' => 1,
        ]);

        $recovery = PasswordRecovery::create([
            'user_id' => $user->id,
            'first_question' => 'Siapa nama guru favoritmu?',
            'second_question' => 'Apa makanan favoritmu?',
            'first_answer' => 'budi',
            'second_answer' => 'nasgor',
        ]);

        $user->update(['password_recovery_id' => $recovery->id]);

        return $user->refresh();
    }

    public function test_password_reset_enforces_stage_order(): void
    {
        $this->createAdmin('admin@example.com');

        $this->post('/admin/password-resets', ['email' => 'admin@example.com'])->assertRedirect();
        $reset = PasswordReset::query()->latest()->first();
        $this->assertNotNull($reset);

        $skip = $this->put("/admin/password-resets/{$reset->token}", ['stage' => 'new_password', 'new_password' => 'secret123', 'new_password_confirmation' => 'secret123']);
        $skip->assertSessionHasErrors();
    }

    public function test_password_reset_full_flow_updates_password(): void
    {
        $this->createAdmin('admin@example.com');

        $this->post('/admin/password-resets', ['email' => 'admin@example.com'])
            ->assertRedirect(route('admin.password-resets.edit', PasswordReset::query()->latest()->firstOrFail()));

        $reset = PasswordReset::query()->latest()->firstOrFail();
        $this->assertSame('questions', $reset->stage);
        $this->assertSame(48, strlen($reset->token));

        $this->get("/admin/password-resets/{$reset->token}/edit")->assertOk();

        $this->put("/admin/password-resets/{$reset->token}", [
            'stage' => 'questions',
            'first_answer' => 'BUDI',
            'second_answer' => 'Nasgor',
        ])->assertRedirect(route('admin.password-resets.edit', $reset->token));

        $this->assertSame('new_password', $reset->refresh()->stage);

        $this->get("/admin/password-resets/{$reset->token}/edit")->assertOk();

        $this->put("/admin/password-resets/{$reset->token}", [
            'stage' => 'new_password',
            'new_password' => 'secret123',
            'new_password_confirmation' => 'secret123',
        ])->assertRedirect(route('admin.login'));

        $this->assertNull(PasswordReset::query()->find($reset->id));

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'secret123'])
            ->assertRedirect(route('admin.datasets.index'));
    }

    public function test_password_reset_rejects_wrong_answers(): void
    {
        $this->createAdmin('admin@example.com');

        $this->post('/admin/password-resets', ['email' => 'admin@example.com'])->assertRedirect();
        $reset = PasswordReset::query()->latest()->firstOrFail();

        $this->put("/admin/password-resets/{$reset->token}", [
            'stage' => 'questions',
            'first_answer' => 'salah',
            'second_answer' => 'salah',
        ])->assertSessionHas('error');

        $this->assertSame('questions', $reset->refresh()->stage);
    }

    public function test_password_reset_rejects_expired_token(): void
    {
        $user = $this->createAdmin('admin@example.com');

        $reset = PasswordReset::query()->create([
            'user_id' => $user->id,
            'token' => 'expiredtokenvalue0123456789012345678901234567',
            'stage' => 'questions',
            'expires_at' => now()->subHour(),
        ]);

        $this->get("/admin/password-resets/{$reset->token}/edit")->assertStatus(410);

        $this->put("/admin/password-resets/{$reset->token}", [
            'stage' => 'questions',
            'first_answer' => 'budi',
            'second_answer' => 'nasgor',
        ])->assertStatus(410);
    }

    public function test_password_reset_store_rejects_unknown_email(): void
    {
        $this->post('/admin/password-resets', ['email' => 'nobody@example.com'])->assertSessionHasErrors('email');
    }

    public function test_legacy_recovery_routes_are_gone(): void
    {
        $this->get('/admin/forgot-password')->assertNotFound();
        $this->get('/admin/question-form')->assertNotFound();
        $this->get('/admin/password-recovery-form')->assertNotFound();
    }

    public function test_password_recovery_edit_update_for_authenticated_admin(): void
    {
        $user = $this->createAdmin('admin@example.com');

        $this->get('/admin/password-recovery/edit')->assertRedirect(route('admin.login'));

        $this->actingAs($user)->get('/admin/password-recovery/edit')->assertOk();

        $this->actingAs($user)->put('/admin/password-recovery', [
            'first_question' => 'Pertanyaan baru satu?',
            'second_question' => 'Pertanyaan baru dua?',
            'first_answer' => 'JAWABAN SATU',
            'second_answer' => 'Jawaban Dua',
        ])->assertRedirect(route('admin.password-recovery.edit'));

        $recovery = $user->refresh()->password_recovery;
        $this->assertSame('Pertanyaan baru satu?', $recovery->first_question);
        $this->assertSame('jawaban satu', $recovery->first_answer);
        $this->assertSame('jawaban dua', $recovery->second_answer);
    }

    public function test_password_recovery_update_validates_input(): void
    {
        $user = $this->createAdmin('admin@example.com');

        $this->actingAs($user)->put('/admin/password-recovery', [
            'first_question' => '',
            'second_question' => 'Pertanyaan baru dua?',
            'first_answer' => 'satu',
            'second_answer' => 'dua',
        ])->assertSessionHasErrors('first_question');
    }
}
