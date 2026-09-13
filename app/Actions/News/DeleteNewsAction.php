<?php

namespace App\Actions\News;

use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Traits\HandlesFileUpload;

class DeleteNewsAction
{
    use HandlesFileUpload;
    public function __construct(
        private NewsRepositoryInterface $news
    ) {
        //
    }
    public function execute(News $news): bool
    {
        $result = $this->news->delete($news);

        if ($result && $news->image) {
            $this->deleteFile($news->image, 'news');
        }

        return $result;
    }
}
