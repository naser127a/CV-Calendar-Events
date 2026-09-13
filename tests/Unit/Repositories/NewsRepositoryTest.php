<?php

namespace Tests\Unit\Repositories;

use App\DTOs\News\NewsFilterData;
use App\Models\News;
use App\Models\User;
use App\Repositories\Eloquent\NewsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private NewsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(NewsRepository::class);
    }

    public function test_repository_can_search_news_by_title(): void
    {
        News::factory()->create([
            'title' => 'Laravel Backend',
        ]);

        News::factory()->create([
            'title' => 'React Frontend',
        ]);

        $filters = new NewsFilterData(
            search: 'Laravel'
        );

        $result = $this->repository->search($filters);

        $this->assertCount(1, $result);
        $this->assertEquals('Laravel Backend', $result->first()->title);
    }

    public function test_repository_can_filter_by_type(): void
    {
        News::factory()->create([
            'type' => 'news',
        ]);

        News::factory()->create([
            'type' => 'announcement',
        ]);

        $filters = new NewsFilterData(
            type: 'announcement'
        );

        $result = $this->repository->search($filters);

        $this->assertCount(1, $result);
        $this->assertEquals(
            'announcement',
            $result->first()->type
        );
    }

    public function test_repository_can_filter_by_source_type(): void
    {
        News::factory()->create([
            'source_type' => 'local',
        ]);

        News::factory()->create([
            'source_type' => 'external',
        ]);

        $filters = new NewsFilterData(
            sourceType: 'external'
        );

        $result = $this->repository->search($filters);

        $this->assertCount(1, $result);
        $this->assertEquals(
            'external',
            $result->first()->source_type
        );
    }

    public function test_repository_can_filter_by_status(): void
    {
        News::factory()->create([
            'status' => true,
        ]);

        News::factory()->create([
            'status' => false,
        ]);

        $filters = new NewsFilterData(
            status: true
        );

        $result = $this->repository->search($filters);

        $this->assertCount(1, $result);
        $this->assertTrue((bool) $result->first()->status);
    }

    public function test_repository_can_filter_by_breaking_status(): void
    {
        News::factory()->create([
            'is_breaking' => true,
        ]);

        News::factory()->create([
            'is_breaking' => false,
        ]);

        $filters = new NewsFilterData(
            isBreaking: true
        );

        $result = $this->repository->search($filters);

        $this->assertCount(1, $result);
        $this->assertTrue((bool) $result->first()->is_breaking);
    }

    public function test_repository_can_filter_by_creator(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        News::factory()->create([
            'created_by' => $user1->id,
        ]);

        News::factory()->create([
            'created_by' => $user2->id,
        ]);

        $filters = new NewsFilterData(
            createdBy: $user1->id
        );

        $result = $this->repository->search($filters);

        $this->assertCount(1, $result);
        $this->assertEquals(
            $user1->id,
            $result->first()->created_by
        );
    }

    public function test_repository_can_paginate_news(): void
    {
        News::factory()->count(12)->create();

        $filters = new NewsFilterData(
            perPage: 5,
            page: 1
        );

        $result = $this->repository->search($filters);

        $this->assertEquals(5, $result->count());
        $this->assertEquals(12, $result->total());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(3, $result->lastPage());
    }

    public function test_repository_can_sort_news(): void
    {
        $old = News::factory()->create([
            'published_at' => now()->subDays(2),
        ]);

        $new = News::factory()->create([
            'published_at' => now(),
        ]);

        $filters = new NewsFilterData(
            sortBy: 'published_at',
            sortOrder: 'asc'
        );

        $result = $this->repository->search($filters);

        $this->assertEquals(
            $old->id,
            $result->first()->id
        );

        $this->assertEquals(
            $new->id,
            $result->get(1)->id
        );
    }
}
