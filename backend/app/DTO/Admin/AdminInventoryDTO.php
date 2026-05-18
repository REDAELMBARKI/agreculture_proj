<?php

namespace App\DTO\Admin;

use App\Models\ProductItem;

class AdminInventoryDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $category,
        public float $quantity,
        public ?string $quantity_unit,
        public string $condition,
        public ?string $harvest_date,
        public ?string $region,
        public string $created_at,
        public ?string $image_url = null
    ) {}

    public static function fromProductItem(ProductItem $item): self
    {
        return new self(
            id: $item->id,
            name: $item->item_name,
            category: $item->product?->superCategory?->name ?? 'Unknown',
            quantity: (float) $item->item_quantity,
            quantity_unit: $item->item_quantity_unit,
            condition: (string) $item->item_condition,
            harvest_date: $item->harvest_date?->toDateString(),
            region: $item->region,
            created_at: $item->created_at?->toDateTimeString() ?? now()->toDateTimeString(),
            image_url: $item->product?->media->first()?->file_path
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'quantity' => $this->quantity,
            'quantity_unit' => $this->quantity_unit,
            'condition' => $this->condition,
            'harvest_date' => $this->harvest_date,
            'region' => $this->region,
            'created_at' => $this->created_at,
            'image_url' => $this->image_url,
        ];
    }
}
