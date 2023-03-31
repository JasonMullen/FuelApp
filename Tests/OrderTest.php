<?php

use PHPUnit\Framework\TestCase;

require_once 'Order.php';

class OrderTest extends TestCase {
    private $order;

    protected function setUp(): void {
        $this->order = new Order('test_orders.db');
    }

    protected function tearDown(): void {
        unset($this->order);
        @unlink('test_orders.db');
    }

    public function testValidFuelType() {
        $this->assertTrue($this->order->is_valid_fuel_type('leaded'));
        $this->assertTrue($this->order->is_valid_fuel_type('unleaded'));
        $this->assertTrue($this->order->is_valid_fuel_type('diesel'));
        $this->assertFalse($this->order->is_valid_fuel_type('invalid_fuel_type'));
    }

    public function testValidGallons() {
        $this->assertTrue($this->order->is_valid_gallons(10.5));
        $this->assertTrue($this->order->is_valid_gallons('5.5'));
        $this->assertFalse($this->order->is_valid_gallons(-5));
        $this->assertFalse($this->order->is_valid_gallons('abc'));
    }

    public function testValidName() {
        $this->assertTrue($this->order->is_valid_name('John Doe'));
        $this->assertTrue($this->order->is_valid_name('John-Doe'));
        $this->assertFalse($this->order->is_valid_name('John Doe123'));
        $this->assertFalse($this->order->is_valid_name('John_Doe'));
    }

    public function testValidEmail() {
        $this->assertTrue($this->order->is_valid_email('test@example.com'));
        $this->assertFalse($this->order->is_valid_email('test@example'));
        $this->assertFalse($this->order->is_valid_email('test@.com'));
    }

    public function testValidPhone() {
        $this->assertTrue($this->order->is_valid_phone('+1234567890'));
        $this->assertTrue($this->order->is_valid_phone('1234567890'));
        $this->assertFalse($this->order->is_valid_phone('123456789'));
        $this->assertFalse($this->order->is_valid_phone('12345678901'));
        $this->assertFalse($this->order->is_valid_phone('123-456-7890'));
    }

    public function testValidPaymentType() {
        $this->assertTrue($this->order->is_valid_payment_type('cash'));
        $this->assertTrue($this->order->is_valid_payment_type('credit'));
        $this->assertTrue($this->order->is_valid_payment_type('debit'));
        $this->assertFalse($this->order->is_valid_payment_type('invalid_payment_type'));
    }

    public function testAddAndGetAllOrders() {
        $this->order->add_order('leaded', 5.5, 'John', 'Doe', 'john@example.com', '+1234567890', 'cash');
        $orders = $this->order->get_all_orders();
        $this->assertCount(1, $orders);
        $this->assertEquals('leaded', $orders[0]['fuel_type']);
        $this->assertEquals(5.5, $orders[0]['gallons']);
    }

    public function testGetOrderHistory() {
        $this->order->add_order('leaded', 
        Finish this code off, start from the last completed line

        5.5, 'John', 'Doe', 'john@example.com', '+1234567890', 'cash');
        $this->order->add_order('unleaded', 10, 'Jane', 'Doe', 'jane@example.com', '+1234567891', 'credit');
        $johnOrders = $this->order->get_order_history('john@example.com');
        $this->assertCount(1, $johnOrders);
        $this->assertEquals('leaded', $johnOrders[0]['fuel_type']);
        $this->assertEquals(5.5, $johnOrders[0]['gallons']);

        $janeOrders = $this->order->get_order_history('jane@example.com');
        $this->assertCount(1, $janeOrders);
        $this->assertEquals('unleaded', $janeOrders[0]['fuel_type']);
        $this->assertEquals(10, $janeOrders[0]['gallons']);
}
?>
