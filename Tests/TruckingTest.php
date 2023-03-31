<?php
  use PHPUnit\Framework\TestCase;

class TruckingTest extends TestCase {
    /**
     * @covers Trucking::calculate_final_cost
     */
    public function testCalculateFinalCost() {
        // Create a mock Order instance with get_all_orders() method
        $order = $this->getMockBuilder(Order::class)
                      ->setMethods(['get_all_orders'])
                      ->getMock();

        // Set up the mock Order instance to return an array with a last order
        $order->expects($this->once())
              ->method('get_all_orders')
              ->willReturn([['2022-01-01', 'Unleaded', 1000]]);

        // Create a mock FuelQuote instance with get_last_quote_location() method
        $fuel_quote = $this->getMockBuilder(FuelQuote::class)
                           ->setMethods(['get_last_quote_location'])
                           ->getMock();

        // Set up the mock FuelQuote instance to return a city and state
        $fuel_quote->expects($this->once())
                   ->method('get_last_quote_location')
                   ->willReturn(['Houston', 'TX']);

        // Create a Trucking instance with a starting location
        $trucking = new Trucking('Dallas, TX');

        // Calculate the final cost for the order and fuel quote
        $final_cost = $trucking->calculate_final_cost($order, $fuel_quote);

        // Assert that the final cost is a positive number
        $this->assertGreaterThanOrEqual(0, $final_cost);
    }
}

?>
