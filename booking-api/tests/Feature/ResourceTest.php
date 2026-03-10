<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // ✅ Vide le cache avant chaque test
    }

    private function createResource(array $overrides = []): Resource
    {
        $category = Category::factory()->create();
        return Resource::factory()->create(array_merge([
            'category_id' => $category->id,
            'is_active'   => true,
        ], $overrides));
    }

    public function test_can_list_resources(): void
    {
        $this->createResource();
        $this->createResource();

        $response = $this->getJson('/api/resources');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'price_per_night', 'capacity']],
                'meta',
            ]);
    }

    public function test_can_filter_resources_by_location(): void
    {
        $this->createResource(['location' => 'Paris']);
        $this->createResource(['location' => 'Lyon']);

        $response = $this->getJson('/api/resources?location=Paris');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_filter_resources_by_capacity(): void
    {
        $this->createResource(['capacity' => 2]);
        $this->createResource(['capacity' => 5]);

        $response = $this->getJson('/api/resources?capacity=4');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_inactive_resources_not_listed(): void
    {
        $this->createResource(['is_active' => true]);
        $this->createResource(['is_active' => false]);

        $response = $this->getJson('/api/resources');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_show_resource_detail(): void
    {
        $resource = $this->createResource();

        $response = $this->getJson("/api/resources/{$resource->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $resource->id);
    }
}
