<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    /**
     * Busca un producto por su ID aplicando un bloqueo de fila para actualizaciones.
     * Lanza ModelNotFoundException si no existe.
     *
     * @param int $id
     * @return Product
     */
    public function findForUpdate(int $id): Product;

    /**
     * Reduce el stock de un producto específico.
     *
     * @param Product $product
     * @param int $quantity
     * @return boolean
     */
    public function decrementStock(Product $product, int $quantity): bool;
}
