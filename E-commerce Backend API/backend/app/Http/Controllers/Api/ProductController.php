<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // GET /products
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $products = $this->productService->getAllProducts($perPage);

        return ProductResource::collection($products)
            ->additional([
                'meta' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ]
            ]);
    }

    // GET /products/{product}
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return new ProductResource($product);
    }

    // POST /products
    public function store(ProductStoreRequest $request)
    {
        $this->authorizeRole(['admin']); // Only admin can create

        $product = $this->productService->createProduct($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    // PUT /products/{product}
    public function update(ProductStoreRequest $request, $id)
    {
        $this->authorizeRole(['admin']); // Only admin can update

        $product = $this->productService->updateProduct($id, $request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(200);
    }

    // DELETE /products/{product}
    public function destroy($id)
    {
        $this->authorizeRole(['admin']); // Only admin can delete

        $this->productService->deleteProduct($id);

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    // Role check helper
    protected function authorizeRole(array $roles)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized');
        }
    }
}
