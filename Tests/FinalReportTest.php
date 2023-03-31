<?php
  <?php
use PHPUnit\Framework\TestCase;

require_once 'FinalReport.php';

/**
 * @covers FinalReport
 */
class FinalReportTest extends TestCase
{
    protected $finalReport;

    protected function setUp(): void
    {
        $this->finalReport = new FinalReport('test_final_reports.db');
    }

    protected function tearDown(): void
    {
        unset($this->finalReport);
        if (file_exists('test_final_reports.db')) {
            unlink('test_final_reports.db');
        }
    }

    /**
     * @covers FinalReport::__construct
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(FinalReport::class, $this->finalReport);
    }

    /**
     * @covers FinalReport::create_table
     */
    public function testCreateTable(): void
    {
        $connection = new SQLite3('test_final_reports.db');
        $result = $connection->querySingle("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='final_reports'");
        $this->assertEquals(1, $result);
        $connection->close();
    }
}

?>
