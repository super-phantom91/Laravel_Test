<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ProductStorageService
{
    private const FILE_PATH = 'products.json';

    public function all(): array
    {
        $products = $this->read();

        usort($products, function (array $a, array $b) {
            return strtotime($a['datetime_submitted']) <=> strtotime($b['datetime_submitted']);
        });

        return array_map(fn (array $product) => $this->withTotalValue($product), $products);
    }

    public function store(array $data): array
    {
        $products = $this->read();

        $product = [
            'id' => $this->nextId($products),
            'product_name' => $data['product_name'],
            'quantity_in_stock' => (int) $data['quantity_in_stock'],
            'price_per_item' => round((float) $data['price_per_item'], 2),
            'datetime_submitted' => now()->toDateTimeString(),
        ];

        $products[] = $product;
        $this->write($products);

        return $this->withTotalValue($product);
    }

    public function update(int $id, array $data): ?array
    {
        $products = $this->read();
        $updated = null;

        foreach ($products as $index => $product) {
            if ($product['id'] !== $id) {
                continue;
            }

            $products[$index] = [
                'id' => $id,
                'product_name' => $data['product_name'],
                'quantity_in_stock' => (int) $data['quantity_in_stock'],
                'price_per_item' => round((float) $data['price_per_item'], 2),
                'datetime_submitted' => $product['datetime_submitted'],
            ];

            $updated = $this->withTotalValue($products[$index]);
            break;
        }

        if ($updated === null) {
            return null;
        }

        $this->write($products);

        return $updated;
    }

    public function sumTotalValue(): float
    {
        return round(array_sum(array_column($this->all(), 'total_value')), 2);
    }

    private function read(): array
    {
        if (! Storage::disk('local')->exists(self::FILE_PATH)) {
            $this->write([]);

            return [];
        }

        $contents = Storage::disk('local')->get(self::FILE_PATH);
        $products = json_decode($contents, true);

        return is_array($products) ? $products : [];
    }

    private function write(array $products): void
    {
        Storage::disk('local')->put(
            self::FILE_PATH,
            json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function nextId(array $products): int
    {
        if ($products === []) {
            return 1;
        }

        return max(array_column($products, 'id')) + 1;
    }

    private function withTotalValue(array $product): array
    {
        $product['total_value'] = round(
            $product['quantity_in_stock'] * $product['price_per_item'],
            2
        );

        return $product;
    }
}
