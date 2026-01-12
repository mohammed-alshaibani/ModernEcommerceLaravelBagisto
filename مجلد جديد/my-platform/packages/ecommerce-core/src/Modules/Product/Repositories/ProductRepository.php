<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Repositories;

use MyPlatform\EcommerceCore\Repositories\BaseRepository;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;
use MyPlatform\EcommerceCore\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
