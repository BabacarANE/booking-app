<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::factory()->create(['role' => 'client']);

        $category = Category::factory()->create();
        $this->resource = Resource::factory()->create([
            'category_id'     => $category->id,
            'price_per_night' => 100,
            'is_active'       => true,
        ]);
    }

    private function actingAsClient()
    {
        return $this->actingAs($this->client, 'sanctum');
    }

    public function test_authenticated_user_can_create_booking(): void
    {
        $response = $this->actingAsClient()
            ->postJson('/api/bookings', [
                'resource_id'    => $this->resource->id,
                'check_in_date'  => now()->addDays(5)->toDateString(),
                'check_out_date' => now()->addDays(8)->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'check_in_date', 'check_out_date', 'total_price', 'status']
            ]);

        $this->assertDatabaseHas('bookings', [
            'user_id'     => $this->client->id,
            'resource_id' => $this->resource->id,
            'total_price' => 300, // 3 nuits × 100€
            'status'      => 'pending',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_booking(): void
    {
        $response = $this->postJson('/api/bookings', [
            'resource_id'    => $this->resource->id,
            'check_in_date'  => now()->addDays(5)->toDateString(),
            'check_out_date' => now()->addDays(8)->toDateString(),
        ]);

        $response->assertStatus(401);
    }

    public function test_booking_conflict_is_detected(): void
    {
        // Créer une réservation confirmée et payée
        Booking::factory()->create([
            'resource_id'      => $this->resource->id,
            'check_in_date'    => now()->addDays(5)->toDateString(),
            'check_out_date'   => now()->addDays(8)->toDateString(),
            'status'           => 'confirmed',
            'payment_status'   => 'succeeded',
            'payment_intent_id'=> 'pi_test123',
        ]);

        // Tenter de réserver les mêmes dates
        $response = $this->actingAsClient()
            ->postJson('/api/bookings', [
                'resource_id'    => $this->resource->id,
                'check_in_date'  => now()->addDays(6)->toDateString(),
                'check_out_date' => now()->addDays(9)->toDateString(),
            ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Ces dates ne sont pas disponibles pour cette ressource.');
    }

    public function test_total_price_is_calculated_correctly(): void
    {
        $response = $this->actingAsClient()
            ->postJson('/api/bookings', [
                'resource_id'    => $this->resource->id,
                'check_in_date'  => now()->addDays(1)->toDateString(),
                'check_out_date' => now()->addDays(4)->toDateString(), // 3 nuits
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total_price', '300.00');
    }

    public function test_user_can_cancel_booking(): void
    {
        $booking = Booking::factory()->create([
            'user_id'    => $this->client->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAsClient()
            ->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'id'     => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_user_cannot_cancel_other_users_booking(): void
    {
        $otherUser = User::factory()->create();
        $booking   = Booking::factory()->create([
            'user_id' => $otherUser->id,
            'status'  => 'pending',
        ]);

        $response = $this->actingAsClient()
            ->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(404);
    }

    public function test_user_can_list_own_bookings(): void
    {
        Booking::factory()->count(3)->create(['user_id' => $this->client->id]);
        Booking::factory()->count(2)->create(); // Autres utilisateurs

        $response = $this->actingAsClient()
            ->getJson('/api/bookings');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }
}
