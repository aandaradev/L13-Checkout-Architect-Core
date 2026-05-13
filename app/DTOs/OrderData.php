<?php

namespace App\DTOs;

use App\Http\Requests\StoreOrderRequest;

readonly class OrderData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $productId,
        public int $quantity,
        public int $userId = 1
    ){}

public static function fromRequest(StoreOrderRequest $request): self {
    return new self(
        productId: (int) $request->validated('product_id'),
        quantity: (int) $request->validated('quantity')
    );
}
}
