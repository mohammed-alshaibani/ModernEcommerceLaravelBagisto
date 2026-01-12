<?php

namespace App\Repositories;

use MyPlatform\EcommerceCore\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use MyPlatform\EcommerceCore\Repositories\BaseRepository;
use MyPlatform\EcommerceCore\Modules\Product\Models\Product;

class CustomProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    // Custom logic here, e.g. different caching strategy
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
