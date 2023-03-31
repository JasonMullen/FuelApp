<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once 'FuelQuote.php';

final class FuelQuoteTest extends TestCase
{
    private $fuelQuote;

    protected function setUp(): void
    {
        $this->fuelQuote = new FuelQuote('test_fuel_quotes.db');
    }

    protected function tearDown(): void
    {
        unlink('test_fuel_quotes.db');
    }

    /**
     * @covers FuelQuote::is_valid_state
     */
    public function testIsValidState(): void
    {
        $this->assertTrue($this->fuelQuote->is_valid_state('TX'));
        $this->assertFalse($this->fuelQuote->is_valid_state('Texas'));
    }

    /**
     * @covers FuelQuote::is_valid_city
     */
    public function testIsValidCity(): void
    {
        $this->assertTrue($this->fuelQuote->is_valid_city('New York'));
        $this->assertFalse($this->fuelQuote->is_valid_city('New York!'));
    }

    /**
     * @covers FuelQuote::is_valid_address
     */
    public function testIsValidAddress(): void
    {
        $this->assertTrue($this->fuelQuote->is_valid_address('123 Main St.'));
        $this->assertFalse($this->fuelQuote->is_valid_address('123 Main St.~'));
    }

    /**
     * @covers FuelQuote::add_quote
     * @covers FuelQuote::get_all_quotes
     */
    public function testAddAndGetAllQuotes(): void
    {
        $this->fuelQuote->add_quote('Company A', 'TX', 'Houston', '123 Main St.');
        $quotes = $this->fuelQuote->get_all_quotes();

        $this->assertCount(1, $quotes);
        $this->assertEquals('Company A', $quotes[0]['company_name']);
        $this->assertEquals('TX', $quotes[0]['state']);
        $this->assertEquals('Houston', $quotes[0]['city']);
        $this->assertEquals('123 Main St.', $quotes[0]['address']);
    }

    /**
     * @covers FuelQuote::get_last_quote_location
     */
    public function testGetLastQuoteLocation(): void
    {
        $this->fuelQuote->add_quote('Company A', 'TX', 'Houston', '123 Main St.');
        $last_location = $this->fuelQuote->get_last_quote_location();

        $this->assertEquals('Houston', $last_location['city']);
        $this->assertEquals('TX', $last_location['state']);
    }

    /**
     * @covers FuelQuote::get_num_quotes
     */
    public function testGetNumQuotes(): void
    {
        $this->fuelQuote->add_quote('Company A', 'TX', 'Houston', '123 Main St.');
        $num_quotes = $this->fuelQuote->get_num_quotes();

        $this->assertEquals(1, $num_quotes);
    }
}
?>
