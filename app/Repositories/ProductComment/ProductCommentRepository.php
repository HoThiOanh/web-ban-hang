<?php

namespace App\Repositories\ProductComment;

use App\Models\ProductComment;
use App\Repositories\BaseRepository;

class ProductCommentRepository extends BaseRepository implements ProductCommentRepositoryInterface
{

    public function getModel()
    {
        return ProductComment::class;
    }


    public function has(string $name)
    {
        // TODO: Implement has() method.
    }

    public function get(string $name)
    {
        // TODO: Implement get() method.
    }

    public function set(string $name, string $value)
    {
        // TODO: Implement set() method.
    }

    public function clear(string $name)
    {
        // TODO: Implement clear() method.
    }
}
