<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Publication;
use App\Models\User;

class AdminPublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_status_returns_only_approved()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $u1 = Publication::factory()->create(['is_approved' => true]);
        $u2 = Publication::factory()->create(['is_approved' => false]);

        $this->actingAs($admin)->get('/admin/publications?status=approved')
            ->assertStatus(200)
            ->assertSee($u1->titre)
            ->assertDontSee($u2->titre);
    }

    public function test_user_search_returns_json()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Jean Dupont']);

        $this->actingAs($admin)->getJson('/admin/users/search?q=Jean')
            ->assertStatus(200)
            ->assertJsonStructure(['results'])
            ->assertJsonFragment(['text' => 'Jean Dupont']);
    }
}
