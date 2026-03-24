<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getAllProducts($perPage = 10)
    {
        return Product::paginate($perPage);
    }

    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    public function createProduct(array $data)
    {
        $data['price'] = (float) $data['price'];
        $data['stock'] = (int) $data['stock'];

        return Product::create($data);
    }

    public function updateProduct($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }
}
