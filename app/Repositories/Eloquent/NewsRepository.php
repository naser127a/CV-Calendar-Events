<?php

namespace App\Repositories\Eloquent;

use App\DTOs\News\NewsFilterData;
use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(News $news)
    {
        $this->model = $news;
    }

    public function search(NewsFilterData $filters): LengthAwarePaginator
    {
        $query =$this->model->newQuery()->with('user');

        if($filters->search) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters->search}%")
                    ->orWhere('summary', 'like', "%{$filters->search}%")
                    ->orWhere('content', 'like', "%{$filters->search}%");
            });
        }

        if($filters->type) {
            $query->where('type', $filters->type);
        }
        if($filters->sourceType) {
            $query->where('source_type', $filters->sourceType);
        }
        if($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        if($filters->isBreaking !== null) {
            $query->where('is_breaking', $filters->isBreaking);
        }

        if($filters->createdBy) {
            $query->where('created_by', $filters->createdBy);
        }

        if($filters->publishedFrom) {
            $query->where('published_at', '>=', $filters->publishedFrom);
        }
        if($filters->publishedTo) {
            $query->where('published_at', '<=', $filters->publishedTo);
        }

        $query->orderBy($filters->sortBy ?? 'published_at', $filters->sortOrder ?? 'desc');

        return $query->paginate($filters->perPage ?? 10, ['*'], 'page', $filters->page ?? 1);

    }
}
