<?php

namespace Tests\Feature;

use App\Enums\NewsPermissionEnum;
use App\Models\News;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create([
            'name' => 'news_manager',
        ]);

        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
        ]);
    }

    private function grant(string $permission): Permission
    {
        $permissionModel = Permission::firstOrCreate([
            'name' => $permission,
        ]);

        $this->role->permissions()->syncWithoutDetaching(
            $permissionModel->id
        );

        return $permissionModel;
    }

    private function auth(): self
    {
        $this->actingAs($this->user, 'sanctum');

        return $this;
    }

    private function newsData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test News',
            'summary' => 'Test summary',
            'content' => 'Test news content',
            'type' => 'news',
            'source_type' => 'local',
            'status' => true,
            'is_breaking' => false,
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_view_news(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        News::factory()->count(3)->create();

        $response = $this->auth()
            ->getJson('/api/v1/news');

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta',
        ]);
    }

    public function test_unauthenticated_user_cannot_view_news(): void
    {
        $response = $this->getJson('/api/v1/news');

        $response->assertUnauthorized();
    }

    public function test_user_without_permission_cannot_view_news(): void
    {
        $response = $this->auth()
            ->getJson('/api/v1/news');

        $response->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_create_news(): void
    {
        $this->grant(NewsPermissionEnum::CREATE->value);

        $response = $this->auth()
            ->postJson('/api/v1/news', $this->newsData());

        $response->assertCreated();

        $response->assertJsonPath(
            'data.title',
            'Test News'
        );

        $this->assertDatabaseHas('news', [
            'title' => 'Test News',
        ]);
    }

    public function test_create_news_requires_title(): void
    {
        $this->grant(NewsPermissionEnum::CREATE->value);

        $data = $this->newsData([
            'title' => null,
        ]);

        $response = $this->auth()
            ->postJson('/api/v1/news', $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('title');
    }

    public function test_create_news_requires_content(): void
    {
        $this->grant(NewsPermissionEnum::CREATE->value);

        $data = $this->newsData([
            'content' => null,
        ]);

        $response = $this->auth()
            ->postJson('/api/v1/news', $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('content');
    }

    public function test_create_news_requires_valid_type(): void
    {
        $this->grant(NewsPermissionEnum::CREATE->value);

        $data = $this->newsData([
            'type' => 'invalid',
        ]);

        $response = $this->auth()
            ->postJson('/api/v1/news', $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('type');
    }

    public function test_create_news_requires_valid_source_type(): void
    {
        $this->grant(NewsPermissionEnum::CREATE->value);

        $data = $this->newsData([
            'source_type' => 'invalid',
        ]);

        $response = $this->auth()
            ->postJson('/api/v1/news', $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('source_type');
    }

    public function test_external_news_requires_external_data(): void
    {
        $this->grant(NewsPermissionEnum::CREATE->value);

        $data = $this->newsData([
            'source_type' => 'external',
        ]);

        $response = $this->auth()
            ->postJson('/api/v1/news', $data);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'external_id',
            'external_url',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_view_single_news(): void
    {
        $this->grant(NewsPermissionEnum::VIEW->value);

        $news = News::factory()->create();

        $response = $this->auth()
            ->getJson("/api/v1/news/{$news->id}");

        $response->assertSuccessful();

        $response->assertJsonPath(
            'data.id',
            $news->id
        );
    }

    public function test_user_without_view_permission_cannot_view_single_news(): void
    {
        $news = News::factory()->create();

        $response = $this->auth()
            ->getJson("/api/v1/news/{$news->id}");

        $response->assertForbidden();
    }

    public function test_view_single_news_returns_not_found_for_missing_news(): void
    {
        $this->grant(NewsPermissionEnum::VIEW->value);

        $response = $this->auth()
            ->getJson('/api/v1/news/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_update_news(): void
    {
        $this->grant(NewsPermissionEnum::UPDATE->value);

        $news = News::factory()->create([
            'title' => 'Old title',
        ]);

        $response = $this->auth()
            ->putJson("/api/v1/news/{$news->id}", [
                'title' => 'Updated title',
                'content' => 'Updated content',
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Updated title',
        ]);
    }

    public function test_user_without_update_permission_cannot_update_news(): void
    {
        $news = News::factory()->create();

        $response = $this->auth()
            ->putJson("/api/v1/news/{$news->id}", [
                'title' => 'Updated title',
            ]);

        $response->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_delete_news(): void
    {
        $this->grant(NewsPermissionEnum::DELETE->value);

        $news = News::factory()->create();

        $response = $this->auth()
            ->deleteJson("/api/v1/news/{$news->id}");

        $response->assertSuccessful();

        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
        ]);
    }

    public function test_user_without_delete_permission_cannot_delete_news(): void
    {
        $news = News::factory()->create();

        $response = $this->auth()
            ->deleteJson("/api/v1/news/{$news->id}");

        $response->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE / DEACTIVATE
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_activate_news(): void
    {
        $this->grant(NewsPermissionEnum::ACTIVATE->value);

        $news = News::factory()->create([
            'status' => false,
        ]);

        $response = $this->auth()
            ->patchJson("/api/v1/news/{$news->id}/toggle-status");

        $response->assertSuccessful();

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'status' => true,
        ]);
    }

    public function test_authorized_user_can_deactivate_news(): void
    {
        $this->grant(NewsPermissionEnum::DEACTIVATE->value);

        $news = News::factory()->create([
            'status' => true,
        ]);

        $response = $this->auth()
            ->patchJson("/api/v1/news/{$news->id}/toggle-status");

        $response->assertSuccessful();

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'status' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BREAKING NEWS
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_mark_news_as_breaking(): void
    {
        $this->grant(NewsPermissionEnum::UPDATE->value);

        $news = News::factory()->create([
            'is_breaking' => false,
        ]);

        $response = $this->auth()
            ->patchJson("/api/v1/news/{$news->id}/toggle-breaking");

        $response->assertSuccessful();

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'is_breaking' => true,
        ]);
    }

    public function test_authorized_user_can_unmark_breaking_news(): void
    {
        $this->grant(NewsPermissionEnum::UPDATE->value);

        $news = News::factory()->create([
            'is_breaking' => true,
        ]);

        $response = $this->auth()
            ->patchJson("/api/v1/news/{$news->id}/toggle-breaking");

        $response->assertSuccessful();

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'is_breaking' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTERING
    |--------------------------------------------------------------------------
    */

    public function test_news_can_be_searched(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        $wanted = News::factory()->create([
            'title' => 'Laravel Testing News',
        ]);

        News::factory()->create([
            'title' => 'Another Article',
        ]);

        $response = $this->auth()
            ->getJson('/api/v1/news?search=Laravel');

        $response->assertSuccessful();

        $response->assertJsonPath(
            'data.0.id',
            $wanted->id
        );
    }

    public function test_news_can_be_filtered_by_type(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        News::factory()->create([
            'type' => 'news',
        ]);

        $announcement = News::factory()->create([
            'type' => 'announcement',
        ]);

        $response = $this->auth()
            ->getJson('/api/v1/news?type=announcement');

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'id' => $announcement->id,
            'type' => 'announcement',
        ]);
    }

    public function test_news_can_be_filtered_by_source_type(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        News::factory()->create([
            'source_type' => 'local',
        ]);

        News::factory()->create([
            'source_type' => 'external',
        ]);

        $response = $this->auth()
            ->getJson('/api/v1/news?source_type=external');

        $response->assertSuccessful();

        $response->assertJsonCount(
            1,
            'data'
        );
    }

    public function test_news_can_be_filtered_by_status(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        $activeNews = News::factory()->create([
            'status' => true,
        ]);

        News::factory()->create([
            'status' => false,
        ]);

        $response = $this->auth()
            ->getJson('/api/v1/news?status=1');

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'id' => $activeNews->id,
            'status' => true,
        ]);
    }
    public function test_news_can_be_filtered_by_breaking_status(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        $breakingNews = News::factory()->create([
            'is_breaking' => true,
        ]);

        News::factory()->create([
            'is_breaking' => false,
        ]);

        $response = $this->auth()
            ->getJson('/api/v1/news?is_breaking=1');

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'id' => $breakingNews->id,
            'is_breaking' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    public function test_news_are_paginated(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        News::factory()->count(12)->create();

        $response = $this->auth()
            ->getJson('/api/v1/news?per_page=5');

        $response->assertSuccessful();

        $response->assertJsonPath(
            'meta.per_page',
            5
        );

        $response->assertJsonPath(
            'meta.total',
            News::count()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SORTING
    |--------------------------------------------------------------------------
    */

    public function test_news_can_be_sorted(): void
    {
        $this->grant(NewsPermissionEnum::VIEW_ALL->value);

        $old = News::factory()->create([
            'published_at' => now()->subYears(10),
        ]);

        $new = News::factory()->create([
            'published_at' => now()->subYears(9),
        ]);

        $response = $this->auth()
            ->getJson('/api/v1/news?sort_by=published_at&sort_order=asc');

        $response->assertSuccessful();

        $data = $response->json('data');

        $oldPosition = collect($data)->search(
            fn($item) => $item['id'] === $old->id
        );

        $newPosition = collect($data)->search(
            fn($item) => $item['id'] === $new->id
        );

        $this->assertNotFalse($oldPosition);
        $this->assertNotFalse($newPosition);

        $this->assertLessThan($newPosition, $oldPosition);
    }
    /* |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    public function test_authorized_user_can_create_news_with_image(): void
    {
        Storage::fake('public');

        $this->grant(NewsPermissionEnum::CREATE->value);

        $image = UploadedFile::fake()->image('news.jpg');

        $response = $this->auth()
            ->post('/api/v1/news', [
                ...$this->newsData(),
                'image' => $image,
            ]);

        $response->assertCreated();

        $news = News::latest()->first();

        $this->assertNotNull($news->image);
    }
}
