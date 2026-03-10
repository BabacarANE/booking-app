<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Resource;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityService $service;
    private Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service  = new AvailabilityService();
        $category       = Category::factory()->create();
        $this->resource = Resource::factory()->create(['category_id' => $category->id]);
    }

    public function test_resource_is_available_when_no_bookings(): void
    {
        $result = $this->service->isAvailable(
            $this->resource->id,
            now()->addDays(5)->toDateString(),
            now()->addDays(8)->toDateString()
        );

        $this->assertTrue($result);
    }

    public function test_resource_is_unavailable_when_confirmed_booking_exists(): void
    {
        Booking::factory()->create([
            'resource_id'       => $this->resource->id,
            'check_in_date'     => now()->addDays(5)->toDateString(),
            'check_out_date'    => now()->addDays(8)->toDateString(),
            'status'            => 'confirmed',
            'payment_status'    => 'succeeded',
            'payment_intent_id' => 'pi_test123',
        ]);

        $result = $this->service->isAvailable(
            $this->resource->id,
            now()->addDays(6)->toDateString(),
            now()->addDays(9)->toDateString()
        );

        $this->assertFalse($result);
    }

    public function test_resource_is_available_after_booking_cancelled(): void
    {
        Booking::factory()->create([
            'resource_id'    => $this->resource->id,
            'check_in_date'  => now()->addDays(5)->toDateString(),
            'check_out_date' => now()->addDays(8)->toDateString(),
            'status'         => 'cancelled',
        ]);

        $result = $this->service->isAvailable(
            $this->resource->id,
            now()->addDays(5)->toDateString(),
            now()->addDays(8)->toDateString()
        );

        $this->assertTrue($result);
    }

    public function test_pending_booking_without_payment_does_not_block(): void
    {
        Booking::factory()->create([
            'resource_id'      => $this->resource->id,
            'check_in_date'    => now()->addDays(5)->toDateString(),
            'check_out_date'   => now()->addDays(8)->toDateString(),
            'status'           => 'pending',
            'payment_status'   => 'pending',
            'payment_intent_id'=> null,
        ]);

        $result = $this->service->isAvailable(
            $this->resource->id,
            now()->addDays(5)->toDateString(),
            now()->addDays(8)->toDateString()
        );

        $this->assertTrue($result);
    }
}
