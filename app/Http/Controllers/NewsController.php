<?php

namespace App\Http\Controllers;

use App\DTOs\News\CreateNewsData;
use App\DTOs\News\NewsFilterData;
use App\DTOs\News\UpdateNewsData;
use App\Helpers\ApiResponse;
use App\Http\Requests\News\NewsFilterRequest;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Services\NewsService;

class NewsController extends Controller
{
    public function __construct(
        private NewsService $news
    ) {}
    public function index(NewsFilterRequest $request)
    {
        $dto = NewsFilterData::fromRequest($request);
        $this->authorize('viewAny', News::class);

        $news = $this->news->search($dto);

        return ApiResponse::paginated(
            NewsResource::collection($news),
            'News retrieved successfully.'
        );
    }

    public function show(News $news)
    {
        $this->authorize('view', $news);

        return ApiResponse::success(
            new NewsResource($news),
            'News retrieved successfully.'
        );
    }

    public function destroy(News $news)
    {
        $this->authorize('delete', $news);

        $this->news->delete($news);

        return ApiResponse::success(
            null,
            'News deleted successfully.'
        );
    }

    public function toggleBreaking(News $news)
    {
        $this->authorize('update', $news);
        if ($news->is_breaking) {
            $this->news->unmarkAsBreaking($news);
        } else {
            $this->news->markAsBreaking($news);
        }
        return ApiResponse::success(
            null,
            'News breaking status toggled successfully.'
        );
    }

    public function toggleStatus(News $news)
    {
        $isCurrentlyActive = (bool) $news->status;
        $ability = $isCurrentlyActive ? 'deactivate' : 'activate';

        $this->authorize($ability, $news);

        if ($isCurrentlyActive) {
            $this->news->deactivate($news);
            $message = 'News deactivated successfully.';
        } else {
            $this->news->activate($news);
            $message = 'News activated successfully.';
        }

        return ApiResponse::success(null, $message);
    }
    public function update(UpdateNewsRequest $request, News $news)
    {

        $this->authorize('update', $news);

        $dto = UpdateNewsData::fromRequest($request);

        $updatedNews = $this->news->update($news, $dto);

        return ApiResponse::success(
            new NewsResource($updatedNews),
            'News updated successfully.'
        );
    }

    public function store(StoreNewsRequest $request)
    {
        $this->authorize('create', News::class);

        $dto = CreateNewsData::fromRequest($request);

        $news = $this->news->create($dto);

        return ApiResponse::success(
            new NewsResource($news),
            'News created successfully.',
            201
        );
    }
}
