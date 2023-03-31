<?php
  class TruckingCost {
    private $db_name;

    function __construct($db_name = "trucking_costs.db") {
        $this->db_name = $db_name;
        $this->create_table();
    }

    // This method creates the trucking_costs table if it doesn't exist.
    function create_table() {
        $connection = new SQLite3($this->db_name);
        $connection->exec('CREATE TABLE IF NOT EXISTS trucking_costs (
                                id INTEGER PRIMARY KEY AUTOINCREMENT,
                                order_id INTEGER NOT NULL,
                                fuel_quote_id INTEGER NOT NULL,
                                final_cost REAL NOT NULL
                            )');
    }

    function add_cost($order_id, $fuel_quote_id, $final_cost) {
        $connection = new SQLite3($this->db_name);
        $query = $connection->prepare('INSERT INTO trucking_costs (order_id, fuel_quote_id, final_cost) VALUES (:order_id, :fuel_quote_id, :final_cost)');
        $query->bindValue(':order_id', $order_id, SQLITE3_INTEGER);
        $query->bindValue(':fuel_quote_id', $fuel_quote_id, SQLITE3_INTEGER);
        $query->bindValue(':final_cost', $final_cost, SQLITE3_FLOAT);
        $query->execute();
    }

    function get_all_costs() {
        $connection = new SQLite3($this->db_name);
        $result_set = $connection->query('SELECT * FROM trucking_costs');
        $results = array();
        while ($row = $result_set->fetchArray(SQLITE3_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }
}class TruckingCost {
    private $db_name;

    function __construct($db_name = "trucking_costs.db") {
        $this->db_name = $db_name;
        $this->create_table();
    }

    // This method creates the trucking_costs table if it doesn't exist.
    function create_table() {
        $connection = new SQLite3($this->db_name);
        $connection->exec('CREATE TABLE IF NOT EXISTS trucking_costs (
                                id INTEGER PRIMARY KEY AUTOINCREMENT,
                                order_id INTEGER NOT NULL,
                                fuel_quote_id INTEGER NOT NULL,
                                final_cost REAL NOT NULL
                            )');
    }

    function add_cost($order_id, $fuel_quote_id, $final_cost) {
        $connection = new SQLite3($this->db_name);
        $query = $connection->prepare('INSERT INTO trucking_costs (order_id, fuel_quote_id, final_cost) VALUES (:order_id, :fuel_quote_id, :final_cost)');
        $query->bindValue(':order_id', $order_id, SQLITE3_INTEGER);
        $query->bindValue(':fuel_quote_id', $fuel_quote_id, SQLITE3_INTEGER);
        $query->bindValue(':final_cost', $final_cost, SQLITE3_FLOAT);
        $query->execute();
    }

    function get_all_costs() {
        $connection = new SQLite3($this->db_name);
        $result_set = $connection->query('SELECT * FROM trucking_costs');
        $results = array();
        while ($row = $result_set->fetchArray(SQLITE3_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }
}
?>
