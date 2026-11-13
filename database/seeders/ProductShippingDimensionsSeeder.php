<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductShippingDimensionsSeeder extends Seeder
{
    /**
     * Add example package dimensions to the products from ProductSeeder.
     */
    public function run(): void
    {
        $dimensionsByBarcode = [
            '7891234567890' => ['width_cm' => 20, 'height_cm' => 10, 'length_cm' => 7, 'weight_kg' => 0.7],
            '7891234567891' => ['width_cm' => 20, 'height_cm' => 10, 'length_cm' => 7, 'weight_kg' => 0.7],
            '7891234567892' => ['width_cm' => 20, 'height_cm' => 10, 'length_cm' => 7, 'weight_kg' => 0.7],
            '7891234567893' => ['width_cm' => 45, 'height_cm' => 35, 'length_cm' => 10, 'weight_kg' => 3.5],
            '7891234567894' => ['width_cm' => 45, 'height_cm' => 35, 'length_cm' => 10, 'weight_kg' => 3.5],
            '7891234567895' => ['width_cm' => 45, 'height_cm' => 35, 'length_cm' => 10, 'weight_kg' => 3.5],
            '7891234567896' => ['width_cm' => 12, 'height_cm' => 10, 'length_cm' => 6, 'weight_kg' => 0.3],
            '7891234567897' => ['width_cm' => 15, 'height_cm' => 10, 'length_cm' => 5, 'weight_kg' => 0.25],
            '7891234567898' => ['width_cm' => 18, 'height_cm' => 14, 'length_cm' => 10, 'weight_kg' => 0.5],
            '7891234567899' => ['width_cm' => 45, 'height_cm' => 35, 'length_cm' => 18, 'weight_kg' => 5.2],
            '7891234567900' => ['width_cm' => 20, 'height_cm' => 15, 'length_cm' => 8, 'weight_kg' => 0.5],
            '7891234567901' => ['width_cm' => 15, 'height_cm' => 15, 'length_cm' => 15, 'weight_kg' => 0.6],
            '7891234567902' => ['width_cm' => 12, 'height_cm' => 8, 'length_cm' => 8, 'weight_kg' => 0.2],
        ];

        foreach ($dimensionsByBarcode as $barcode => $dimensions) {
            $product = Product::query()->where('bar_code', $barcode)->first();

            if (!$product) {
                continue;
            }

            $missingDimensions = array_filter(
                $dimensions,
                static fn (string $field): bool => $product->{$field} === null,
                ARRAY_FILTER_USE_KEY
            );

            if ($missingDimensions !== []) {
                $product->fill($missingDimensions)->save();
            }
        }
    }
}
