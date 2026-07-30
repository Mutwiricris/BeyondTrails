<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_update_profile()
    {
        $response = $this->putJson('/api/v1/users/profile', [
            'name' => 'New Name',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_update_name()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($user)->putJson('/api/v1/users/profile', [
            'first_name' => 'New',
            'last_name' => 'Name',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.user.name', 'New Name');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_authenticated_user_can_update_selectables_without_overwriting_existing()
    {
        $user = User::factory()->create([
            'selectables' => [
                'phone' => '1234567890',
                'city' => 'Nairobi'
            ],
        ]);

        $response = $this->actingAs($user)->putJson('/api/v1/users/profile', [
            'selectables' => [
                'city' => 'Mombasa',
                'country' => 'Kenya'
            ],
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('1234567890', $user->selectables['phone']);
        $this->assertEquals('Mombasa', $user->selectables['city']);
        $this->assertEquals('Kenya', $user->selectables['country']);
    }
}
