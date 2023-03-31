<?php
// Define the classes for the application
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
class Order {
    private $db_name;

    public function __construct($db_name = 'orders.db') {
        $this->db_name = $db_name;
        $this->create_table();
    }
    private function create_table() {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->exec(
            "CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                fuel_type TEXT NOT NULL,
                gallons REAL NOT NULL,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                payment_type TEXT NOT NULL
            )"
        );
    }
    public function add_order($fuel_type, $gallons, $first_name, $last_name, $email, $phone, $payment_type) {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare(
            "INSERT INTO orders (fuel_type, gallons, first_name, last_name, email, phone, payment_type)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $statement->execute([$fuel_type, $gallons, $first_name, $last_name, $email, $phone, $payment_type]);
    }
    public function get_all_orders() {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare("SELECT * FROM orders");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_order_history($email) {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare("SELECT * FROM orders WHERE email = ?");
        $statement->execute([$email]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function is_valid_fuel_type($fuel_type) {
        return in_array(strtolower($fuel_type), ['leaded', 'unleaded', 'diesel']);
    }
    public function is_valid_gallons($gallons) {
        $float_gallons = filter_var($gallons, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return (float)$float_gallons > 0;
    }
    public function is_valid_name($name) {
        $name_pattern = '/^[A-Za-z\s\-]+$/';
        return preg_match($name_pattern, $name) === 1;
    }
    public function is_valid_email($email) {
        $email_pattern = '/^[\w\.-]+@[\w\.-]+\.\w+$/';
        return preg_match($email_pattern, $email) === 1;
    }
    public function is_valid_phone($phone) {
        $phone_pattern = '/^\+?\d{10}$/';
        return preg_match($phone_pattern, $phone) === 1;
    }
    public function is_valid_payment_type($payment_type) {
        return in_array(strtolower($payment_type), ['cash', 'credit', 'debit']);
    }
}
require_once __DIR__ . '/vendor/autoload.php';

use Geocoder\Provider\OpenStreetMap;
use Geocoder\Query\GeocodeQuery;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\StatefulGeocoder;




class Trucking {
    public $db_name;
    private $geocoder;
    private $starting_location;

    
    public function __construct($starting_location) {
        $this->geocoder = new StatefulGeocoder(new OpenStreetMap(), null, new CurlHttpAdapter());
        $this->starting_location = $starting_location;
    }

    public function get_distance($destination_address) {
        $starting_location = $this->geocoder->geocodeQuery(GeocodeQuery::create($this->starting_location));
        $destination_location = $this->geocoder->geocodeQuery(GeocodeQuery::create($destination_address));

        if ($starting_location && $destination_location) {
            $starting_point = [$starting_location->getCoordinates()->getLatitude(), $starting_location->getCoordinates()->getLongitude()];
            $destination_point = [$destination_location->getCoordinates()->getLatitude(), $destination_location->getCoordinates()->getLongitude()];
            $distance_in_miles = $this->distance($starting_point, $destination_point, 'mi');
            return $distance_in_miles;
        } else {
            return null;
        }
    }

    public function calculate_final_cost($order, $fuel_quote) {
        // Constants
        $MAX_CAPACITY = 3000;
        //The gas price changes based on the selected fuel by the user
        $PRICES_PER_GALLON = [
            "Leaded" => 3.047,
            "Unleaded" => 3.760,
            "Diesel" => 3.793
        ];

        $MPG = 6.5;
        // Get the last order, fuel type, and quote from the order and fuel_quote instances
        $orders = $order->get_all_orders();
        $last_order = end($orders);
        $fuel_type = $last_order[1];
        $gallons = $last_order[2];
        $city_state = $fuel_quote->get_last_quote_location();
        $destination_address = $city_state[0] . ', ' . $city_state[1];

        if ($gallons <= 0) {
            return null;
        }

        $distance_in_miles = $this->get_distance($destination_address);

        if ($distance_in_miles === null) {
            return null;
        }

        $distance_cost = $distance_in_miles / $MPG;

        //Get the price per gallon for the selected fuel type
        $price_per_gallon = $PRICES_PER_GALLON[$fuel_type];

        // Calculate the number of trucks needed
        $num_trucks = ceil($gallons / $MAX_CAPACITY);
        $remaining_gallons = $gallons;

        // Calculate the final cost based on the number of trucks
        $final_cost = 0;
        for ($i = 0; $i < $num_trucks; $i++) {
            if ($remaining_gallons > $MAX_CAPACITY) {
                $gallons_on_truck = $MAX_CAPACITY;
            } else {
                $gallons_on_truck = $remaining_gallons;
            }

            $cost_for_truck = $gallons_on_truck * $price_per_gallon + $distance_cost;
            $final_cost += $cost_for_truck;
            $remaining_gallons -= $MAX_CAPACITY;
        }

        return $final_cost;
    }

    private function distance($from, $to, $unit) {
        $theta = $from[1] - $to[1];
        $dist = sin(deg2rad($from[0])) * sin(deg2rad($to[0])) +  cos(deg2rad($from[0])) * cos(deg2rad($to[0])) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtolower($unit);
        if ($unit == "km") {
            return ($miles * 1.609344);
        } else if ($unit == "nmi") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}
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
}
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

public function add_report($order, $fuel_quote, $trucking, $final_cost) {
    // Extract the required information from the instances of the other classes
    $last_order = $order->get_all_orders()[-1];
    list($fuel_type, $gallons, $first_name, $last_name, $email, $phone_number, $payment_method) = array_slice($last_order, 1);

    $last_quote = $fuel_quote->get_all_quotes()[-1];
    list($company_name, $state, $city, $address) = array_slice($last_quote, 1);

    $distance = $trucking->get_distance($city . ', ' . $state);

    $connection = new SQLite3($this->db_name);
    $stmt = $connection->prepare("INSERT INTO final_reports (
        company_name,
        state,
        city,
        address,
        fuel_type,
        gallons,
        first_name,
        last_name,
        email,
        phone_number,
        payment_method,
        distance,
        final_cost
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $company_name, SQLITE3_TEXT);
    $stmt->bindValue(2, $state, SQLITE3_TEXT);
    $stmt->bindValue(3, $city, SQLITE3_TEXT);
    $stmt->bindValue(4, $address, SQLITE3_TEXT);
    $stmt->bindValue(5, $fuel_type, SQLITE3_TEXT);
    $stmt->bindValue(6, $gallons, SQLITE3_INTEGER);
    $stmt->bindValue(7, $first_name, SQLITE3_TEXT);
    $stmt->bindValue(8, $last_name, SQLITE3_TEXT);
    $stmt->bindValue(9, $email, SQLITE3_TEXT);
    $stmt->bindValue(10, $phone_number, SQLITE3_TEXT);
    $stmt->bindValue(11, $payment_method, SQLITE3_TEXT);
    $stmt->bindValue(12, $distance, SQLITE3_FLOAT);
    $stmt->bindValue(13, $final_cost, SQLITE3_FLOAT);
    $stmt->execute();
    $stmt->close();
    $connection->close();
}

public function get_all_reports() {
    $connection = new SQLite3($this->db_name);
    $result = $connection->query('SELECT * FROM final_reports');
    $reports = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $reports[] = $row;
    }
    $result->finalize();
    $connection->close();
    return $reports;
    }
}
?>
