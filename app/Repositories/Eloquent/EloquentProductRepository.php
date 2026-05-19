<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class EloquentProductRepository implements ProductRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {}

    /**
     * Busca un producto por su ID con findOrFail para el manejo de errores.
     *
     * @param int $id
     * @return Product
     */
    public function findForUpdate(int $id): Product
    {
        // Aquí encapsulamos el lockForUpdate que antes estaba en el Action.
        return Product::lockForUpdate()->findOrFail($id);
    }

    /**
     * Actualiza el stock.
     *
     * @param Product $product
     * @param int $quantity
     * @return boolean
     */
    public function decrementStock(Product $product, int $quantity): bool
    {
        return $product->decrement('stock', $quantity);
    }
}
