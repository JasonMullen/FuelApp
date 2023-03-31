<?php
  class FinalReport{
    public $db_name;
    public function __construct($db_name='final_reports.db') {
    $this->db_name = $db_name;
    $this->create_table();
}

private function create_table() {
    $connection = new SQLite3($this->db_name);
    $connection->exec('CREATE TABLE IF NOT EXISTS final_reports (
                          id INTEGER PRIMARY KEY AUTOINCREMENT,
                          company_name TEXT NOT NULL,
                          state TEXT NOT NULL,
                          city TEXT NOT NULL,
                          address TEXT NOT NULL,
                          fuel_type TEXT NOT NULL,
                          gallons INTEGER NOT NULL,
                          first_name TEXT NOT NULL,
                          last_name TEXT NOT NULL,
                          email TEXT NOT NULL,
                          phone_number TEXT NOT NULL,
                          payment_method TEXT NOT NULL,
                          distance REAL NOT NULL,
                          final_cost REAL NOT NULL)');
    $connection->close();
}
?>
