<?php

// Change to '../src/SuperMarket.php' if you want to run phpunit tests locally
require_once '/var/www/html/src/SuperMarket.php';

use PHPUnit\Framework\TestCase;
use src\Checkout;
use src\DiscountedQuantityRules;
use src\Item;

class SuperMarketTest extends TestCase
{
    private function getCheckout(): Checkout
    {
        // Create Super Market Items
        $items['A'] = new Item('A', 'Item A', 50);
        $items['B'] = new Item('B', 'Item B', 30);
        $items['C'] = new Item('C', 'Item C', 20);
        $items['D'] = new Item('D', 'Item D', 15);

        // Create Super Market Item Rules
        $itemADiscountQuantityRules = new DiscountedQuantityRules($items['A']->getPrice());
        $itemBDiscountQuantityRules = new DiscountedQuantityRules($items['B']->getPrice());

        $itemADiscountQuantityRules->addDiscountQuantityRule(130, 3);
        $itemBDiscountQuantityRules->addDiscountQuantityRule(45, 2);

        // It is possible to include extra rules for example:
        //$itemADiscountQuantityRules->addDiscountQuantityRule( 200, 5);
        //$itemADiscountQuantityRules->addDiscountQuantityRule( 400, 10);

        // Add a discounted rule for item A AND B
        // Only one rule per item allowed
        $items['A']->setDiscountRule($itemADiscountQuantityRules);
        $items['B']->setDiscountRule($itemBDiscountQuantityRules);

        // It is possible to include new rules for example:
        //$items['B']->setDiscountRule(new FixedPriceDiscountRule($items['B']->getPrice(), 75));

        return new Checkout($items);
    }

    public function testTotals()
    {
        $checkout = $this->getCheckout();

        $this->assertEquals(0, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A']);
        $this->assertEquals(50, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'B']);
        $this->assertEquals(80, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['C', 'D', 'B', 'A']);
        $this->assertEquals(115, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A']);
        $this->assertEquals(100, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A']);
        $this->assertEquals(130, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A', 'A']);
        $this->assertEquals(180, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A', 'A', 'A']);
        $this->assertEquals(230, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A', 'A', 'A', 'A']);
        $this->assertEquals(260, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A', 'B']);
        $this->assertEquals(160, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A', 'B', 'B']);
        $this->assertEquals(175, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['A', 'A', 'A', 'B', 'B', 'D']);
        $this->assertEquals(190, $checkout->calculateTotal());
        $checkout->clear();

        $checkout->setItems(['D', 'A', 'B', 'A', 'B', 'A']);
        $this->assertEquals(190, $checkout->calculateTotal());
        $checkout->clear();
    }

    public function testIncremental()
    {
        $checkout = $this->getCheckout();

        $this->assertEquals(0, $checkout->calculateTotal());

        $checkout->scan('A');
        $this->assertEquals(50, $checkout->calculateTotal());

        $checkout->scan('B');
        $this->assertEquals(80, $checkout->calculateTotal());

        $checkout->scan('A');
        $this->assertEquals(130, $checkout->calculateTotal());

        $checkout->scan('A');
        $this->assertEquals(160, $checkout->calculateTotal());

        $checkout->scan('B');
        $this->assertEquals(175, $checkout->calculateTotal());
    }
}
