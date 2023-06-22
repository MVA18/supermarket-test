<?php
namespace src;
interface Rule
{
    public function getDiscountedPrice(int $quantity = NULL): float;
}

// Example of how and extra rule can be added
class FixedPriceDiscountRule implements Rule
{
    private int $regularPrice;
    private float $discount;

    public function __construct(float $regularPrice, float $discount)
    {
        $this->regularPrice = $regularPrice;
        $this->discount = $discount / 100;
    }

    public function getDiscountedPrice(int $quantity = NULL): float
    {
        return $this->regularPrice * $this->discount;
    }
}

class DiscountedQuantityRules implements Rule
{
    private float $regularPrice;

    private array $quantityRules = [];

    public function __construct(float $regularPrice)
    {
        $this->regularPrice = $regularPrice;
    }

    public function addDiscountQuantityRule(float $discountPrice, int $discountValidQuantity): self
    {
        $this->quantityRules[$discountValidQuantity] = $discountPrice;
        return $this;
    }

    public function getDiscountQuantityRules(): array
    {
        krsort($this->quantityRules);
        return $this->quantityRules;
    }

    public function getDiscountedPrice(int $quantity = NULL): float
    {
        $total = 0.0;
        $itemsLeft = $quantity;
        foreach ($this->getDiscountQuantityRules() as $discountQuantity => $discountPrice) {
            if ($itemsLeft >= $discountQuantity) {
                $specialPriceItems = floor($itemsLeft / $discountQuantity);
                $total += $specialPriceItems * $discountPrice;
                $itemsLeft -= $specialPriceItems * $discountQuantity;
            }
        }
        $total += $itemsLeft * $this->regularPrice;
        return $total;
    }
}

class Item
{
    private string $id;
    private string $name;
    private float $price;
    private ?Rule $discountRule = null;

    public function __construct(string $id, string $name, string $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->id;
    }

    public function getDiscountRule(): ?Rule
    {
        $this->discount;
    }

    public function setDiscountRule(Rule $rule): self
    {
        $this->discountRule = $rule;
        return $this;
    }

    public function getTotalPrice($quantity): float
    {
        $price = $this->getPrice() * $quantity;
        if ($this->discountRule) {
            $price = $this->discountRule->getDiscountedPrice($quantity);
        }

        return $price;
    }
}

class Checkout
{
    private array $allItems = [];
    private array $items = [];

    public function __construct(array $allItems)
    {
        $this->allItems = $allItems;
    }

    public function scan(string $itemId): void
    {
        $item = $this->getItemById($itemId);
        $this->items[] = $item;
    }

    public function setItems(array $itemIds): void
    {
        foreach ($itemIds as $itemId) {
            $this->items[] = $this->getItemById($itemId);
        }
    }

    public function calculateTotal(): float
    {
        $itemCounts = $this->countItems();
        $total = 0.0;
        if ($itemCounts) {
            foreach ($itemCounts as $itemId => $quantity) {
                $item = $this->getItemById($itemId);
                if ($item) {
                    $total += $item->getTotalPrice($quantity);
                }
            }
        }
        return $total;
    }

    private function countItems(): array
    {
        $itemCounts = [];

        foreach ($this->items as $item) {
            $id = $item->getId();
            $itemCounts[$id] ??= 0;
            $itemCounts[$id]++;
        }

        return $itemCounts;
    }

    private function getItemById(string $id): ?Item
    {
        foreach ($this->allItems as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }

        return null;
    }

    public function clear(): self
    {
        $this->items = [];
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

}

