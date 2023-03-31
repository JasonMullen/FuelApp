<?php
class FuelQuote {
    public $db_name;

    function __construct($db_name='fuel_quotes.db') {
        $this->db_name = $db_name;
        $this->create_table();
    }

    // Create fuel_quotes table in the database
    function create_table() {
        $connection = new SQLite3($this->db_name);
        $connection->exec('CREATE TABLE IF NOT EXISTS fuel_quotes
            (id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_name TEXT NOT NULL,
            state TEXT NOT NULL,
            city TEXT NOT NULL,
            address TEXT NOT NULL)');
    }

    // Add a fuel quote to the database
    function add_quote($company_name, $state, $city, $address) {
        $connection = new SQLite3($this->db_name);
        $stmt = $connection->prepare('INSERT INTO fuel_quotes (company_name, state, city, address)
            VALUES (:company_name, :state, :city, :address)');
        $stmt->bindValue(':company_name', $company_name, SQLITE3_TEXT);
        $stmt->bindValue(':state', $state, SQLITE3_TEXT);
        $stmt->bindValue(':city', $city, SQLITE3_TEXT);
        $stmt->bindValue(':address', $address, SQLITE3_TEXT);
        $stmt->execute();
    }

    // Get all fuel quotes from the database
    function get_all_quotes() {
        $connection = new SQLite3($this->db_name);
        $result = $connection->query('SELECT * FROM fuel_quotes');
        $quotes = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($quotes, $row);
        }
        return $quotes;
    }

    function get_last_quote_location() {
        $connection = new SQLite3($this->db_name);
        $stmt = $connection->prepare('SELECT city, state FROM fuel_quotes ORDER BY id DESC LIMIT 1');
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    function get_num_quotes() {
        $connection = new SQLite3($this->db_name);
        $result = $connection->query('SELECT COUNT(*) FROM fuel_quotes');
        return $result->fetchArray(SQLITE3_NUM)[0];
    }
          
    // Validate state input
    function is_valid_state($state) {
        $state_pattern = '/^[A-Za-z]{2}$/';
        return preg_match($state_pattern, $state);
    }

    // Validate city input
    function is_valid_city($city) {
        $city_pattern = '/^[A-Za-z\s\-]+$/';
        return preg_match($city_pattern, $city);
    }

    // Validate address input
    function is_valid_address($address) {
        $address_pattern = '/^[\d\sA-Za-z,.\-]+$/';
        return preg_match($address_pattern, $address);
    }
}
?>
