<?php
// TruckingCostTest.php

use PHPUnit\Framework\TestCase;

require_once 'TruckingCost.php';

/**
 * @covers TruckingCost
 */
class TruckingCostTest extends TestCase
{
    private $truckingCost;

    protected function setUp(): void
    {
        $this->truckingCost = new TruckingCost(':memory:');
    }

    protected function tearDown(): void
    {
        $this->truckingCost = null;
    }

    public function testAddAndGetAllCosts()
    {
        $this->truckingCost->add_cost(1, 1, 1200.50);
        $this->truckingCost->add_cost(2, 2, 1500.75);

        $all_costs = $this->truckingCost->get_all_costs();

        $this->assertCount(2, $all_costs);

        $this->assertEquals(1, $all_costs[0]['order_id']);
        $this->assertEquals(1, $all_costs[0]['fuel_quote_id']);
        $this->assertEquals(1200.50, $all_costs[0]['final_cost']);

        $this->assertEquals(2, $all_costs[1]['order_id']);
        $this->assertEquals(2, $all_costs[1]['fuel_quote_id']);
        $this->assertEquals(1500.75, $all_costs[1]['final_cost']);
    }
}

?>
