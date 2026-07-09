<?php

namespace App\Http\Controllers;

use App\Services\ProductStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductStorageService $storage
    ) {}

    public function index(): View
    {
        return view('products.index');
    }

    public function list(): JsonResponse
    {
        return response()->json([
            'products' => $this->storage->all(),
            'sum_total_value' => $this->storage->sumTotalValue(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'quantity_in_stock' => ['required', 'integer', 'min:0'],
            'price_per_item' => ['required', 'numeric', 'min:0'],
        ]);

        $product = $this->storage->store($validated);

        return response()->json([
            'message' => 'Product saved successfully.',
            'product' => $product,
            'products' => $this->storage->all(),
            'sum_total_value' => $this->storage->sumTotalValue(),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'quantity_in_stock' => ['required', 'integer', 'min:0'],
            'price_per_item' => ['required', 'numeric', 'min:0'],
        ]);

        $product = $this->storage->update($id, $validated);

        if ($product === null) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product,
            'products' => $this->storage->all(),
            'sum_total_value' => $this->storage->sumTotalValue(),
        ]);
    }
}
