<?php

namespace App\Services;

use App\Actions\News\CreateNewsAction;
use App\Actions\News\DeleteNewsAction;
use App\Actions\News\UpdateNewsAction;
use App\DTOs\News\CreateNewsData;
use App\DTOs\News\NewsFilterData;
use App\DTOs\News\UpdateNewsData;
use App\Events\NewsCreated;
use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class NewsService extends BaseService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private NewsRepositoryInterface $news,
        private CreateNewsAction $createNewsAction,
        private UpdateNewsAction $updateNewsAction,
        private DeleteNewsAction $deleteNewsAction,
        private ActivityLogService $activityLog,

    ) {
        parent::__construct($news);
    }

    public function create(CreateNewsData $data): News
    {
        return DB::transaction(function () use ($data) {
            $userId = Auth::id();

            $news = $this->createNewsAction->execute(
                $data,
                $userId
            );
            event(new NewsCreated(
                news: $news,
                userId: $userId
            ));
            return $news;
        });
    }

    public function update(
        News $news,
        UpdateNewsData $data
    ): News {
        return DB::transaction(function () use ($news, $data) {

            $updatedNews = $this->updateNewsAction->execute(
                $news,
                $data
            );
            return $updatedNews;
        });
    }

    public function delete(News $news): bool
    {
        return DB::transaction(function () use ($news) {

            return $this->deleteNewsAction->execute($news);
        });
    }

    public function search(NewsFilterData $filters): LengthAwarePaginator
    {
        return $this->news->search($filters);
    }

    public function activate(News $news): News
    {
        $updatedNews = $this->news->update($news, [
            'status' => true,
        ]);

        $this->activityLog->log(
            event: 'activated',
            subject: $updatedNews,

        );

        return $updatedNews;
    }
    public function deactivate(News $news): News
    {
        $updatedNews = $this->news->update($news, [
            'status' => false,
        ]);

        $this->activityLog->log(
            event: 'deactivated',
            subject: $updatedNews,

        );

        return $updatedNews;
    }

    public function markAsBreaking(
        News $news,
        ?string $breakingUntil = null
    ): News {
        $updatedNews = $this->news->update($news, [
            'is_breaking' => true,
            'breaking_until' => $breakingUntil,
        ]);

        $this->activityLog->log(
            event: 'breaking_marked',
            subject: $updatedNews,

        );

        return $updatedNews;
    }

    public function unmarkAsBreaking(News $news): News
    {
        $updatedNews = $this->news->update($news, [
            'is_breaking' => false,
            'breaking_until' => null,
        ]);

        $this->activityLog->log(
            event: 'breaking_unmarked',
            subject: $updatedNews,
        );

        return $updatedNews;
    }
}
