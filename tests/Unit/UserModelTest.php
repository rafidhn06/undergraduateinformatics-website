<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_guards_non_fillable_attributes(): void
    {
        $user = User::factory()->create(['email' => 'guard@example.com']);

        try {
            $user->update(['id' => 9999]);
            $this->fail('Updating a non-fillable attribute should be rejected.');
        } catch (MassAssignmentException) {
            $this->assertNotSame(9999, $user->fresh()->id);
        }

        $user->update(['email' => 'kept@example.com']);

        $this->assertSame('kept@example.com', $user->fresh()->email);
    }
}
