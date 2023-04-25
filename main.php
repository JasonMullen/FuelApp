<?php
namespace MyNamespace;

use DateTime;
use PDO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use SQLite3;
require_once __DIR__ . '/vendor/autoload.php';

include_once 'components/nav-bar.php';
include("server/connection.php");

// Your PHP code to process the form data and calculate the suggested price and total amount
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gallonsRequested = $_POST['gallonsRequested'];
    $fuelType = $_POST['fuelType'];
    $deliveryDate = $_POST['deliveryDate'];
    
    // Add your logic to calculate the suggested price and total amount here
    $pricePerGallon = 0; // Replace with your calculation
    $totalAmount = 0; // Replace with your calculation

    $response = array(
        "pricePerGallon" => $pricePerGallon,
        "totalAmount" => $totalAmount
    );

    echo json_encode($response);
}


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
        return (bool) preg_match($state_pattern, $state);
    }
    // Validate city input
    function is_valid_city($city) {
        $city_pattern = '/^[A-Za-z\s\-]+$/';
        return (bool) preg_match($city_pattern, $city);
    }
    // Validate address input
    function is_valid_address($address) {
        $address_pattern = '/^[\d\sA-Za-z,.\-]+$/';
        return (bool) preg_match($address_pattern, $address);
    }

    public function get_last_quote() {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare("SELECT * FROM fuel_quotes ORDER BY id DESC LIMIT 1");
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}

class Order {
    private $db_name;

    // Constructor: Initializes the database and creates the table if it doesn't exist
    public function __construct($db_name = 'orders.db') {
        $this->db_name = $db_name;
        $this->create_table();
    }

    // Create the orders table in the database
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
                payment_type TEXT NOT NULL,
                order_date TEXT NOT NULL
            )"
        );
    }

    // Add a new order to the database
    public function add_order($fuel_type, $gallons, $first_name, $last_name, $email, $phone, $payment_type, $order_date) {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare(
            "INSERT INTO orders (fuel_type, gallons, first_name, last_name, email, phone, payment_type, order_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $statement->execute([$fuel_type, $gallons, $first_name, $last_name, $email, $phone, $payment_type, $order_date]);
    }

    // Get all orders from the database
    public function get_all_orders() {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare("SELECT * FROM orders");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get order history for a specific email
    public function get_order_history($email) {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare("SELECT * FROM orders WHERE email = ?");
        $statement->execute([$email]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validation methods for fuel type, gallons, name, email, phone, and payment type
    // ...

    // Get the last order from the database
    public function get_last_order() {
        $connection = new PDO("sqlite:$this->db_name");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare("SELECT * FROM orders ORDER BY id DESC LIMIT 1");
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}

class GoogleMapsDistanceCalculator {
    private $client;
    private $baseUrl;
    private $apiKey = 'AIzaSyDVaKfhI8-9bXexXD6Y3ODbeACWN6S8AXk'; // Replace with your actual Google Maps API key
    private $db;

    public function __construct($apiKey, $db) {
        $this->client = new Client();
        $this->baseUrl = 'https://maps.googleapis.com/maps/api';
        $this->apiKey = $apiKey;
        $this->db = $db;
    }

    public function get_all_orders() {
        $orders = $this->db->query('SELECT * FROM orders');
        return $orders;
    }

    public function getDistanceToDestination($startingAddress, $destinationAddress) {
        $startingLocation = $this->geocodeAddress($startingAddress);
        $destinationLocation = $this->geocodeAddress($destinationAddress);

        return $this->calculateDistance($startingLocation, $destinationLocation);
    }

    private function geocodeAddress($address) {
        try {
            $response = $this->client->get("{$this->baseUrl}/geocode/json", [
                'query' => [
                    'address' => $address,
                    'key' => $this->apiKey,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            if ($data['status'] == 'OK') {
                return [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                ];
            }
        } catch (RequestException $e) {
            // Handle API request errors
        }
        return null;
    }

    private function calculateDistance($location1, $location2) {
        $lat1 = deg2rad($location1['lat']);
        $lng1 = deg2rad($location1['lng']);
        $lat2 = deg2rad($location2['lat']);
        $lng2 = deg2rad($location2['lng']);
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanceInMiles = 3959 * $c; // Earth's radius in miles
        return round($distanceInMiles); // Round the distance to a whole number
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
        $fuel_type = $last_order['fuel_type'];
        $gallons = $last_order['gallons'];
        $city_state = $fuel_quote->get_last_quote_location();
        $destination_address = $city_state['city'] . ', ' . $city_state['state'];
        $order_address = $last_order['address'];
        $deliveryDate = new DateTime($last_order['deliveryDate']);
        $currentDate = new DateTime();
    
        if ($gallons <= 0) {
            return null;
        }
    
        $distance_in_miles = $this->getDistanceToDestination($order_address, $destination_address);
    
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
    
        // Calculate the difference in days
        $difference = $deliveryDate->diff($currentDate)->days;
    
        // Deduct 1 cent per day from the final cost
        $deduction = $difference * 0.01;
        $final_cost -= $deduction;
    
        return round($final_cost, 2); // Round the final cost to two decimal places
    }
    
    

    
}

class TruckingCost {
    private $db_name;
    private $order;
    private $fuel_quote;
    private $distance_calculator;

    function __construct(Order $order, FuelQuote $fuel_quote, GoogleMapsDistanceCalculator $distance_calculator, $db_name = "trucking_costs.db") {
        $this->db_name = $db_name;
        $this->order = $order;
        $this->fuel_quote = $fuel_quote;
        $this->distance_calculator = $distance_calculator;
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

    function add_cost() {
        $last_order = $this->order->get_last_order();
        $last_fuel_quote = $this->fuel_quote->get_last_quote();
        $final_cost = $this->distance_calculator->calculate_final_cost($last_order, $last_fuel_quote);

        $connection = new SQLite3($this->db_name);
        $query = $connection->prepare('INSERT INTO trucking_costs (order_id, fuel_quote_id, final_cost) VALUES (:order_id, :fuel_quote_id, :final_cost)');
        $query->bindValue(':order_id', $last_order['id'], SQLITE3_INTEGER);
        $query->bindValue(':fuel_quote_id', $last_fuel_quote['id'], SQLITE3_INTEGER);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data from POST request
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $quantity = floatval($_POST['quantity']);
    $fuel_price = floatval($_POST['fuel_price']);

    // Create instances of required classes
    $order = new Order();
    $fuel_quote = new FuelQuote();
    $distance_calculator = new GoogleMapsDistanceCalculator($origin, $destination);
    $trucking_cost = new TruckingCost($order, $fuel_quote, $distance_calculator);

    // Add form data to respective classes
    $order->add_order($origin, $destination, $quantity, $quote_id, $user_id, $gallons_requested, $suggested_price, $total_amount_due);
    $fuel_quote->add_quote($quote_id, $user_id, $gallons_requested, $suggested_price);
    $trucking_cost->add_cost();
    
    
    // Redirect to the confirmation page
    header("Location: confirmation.html");
    exit;
} else {
    // Redirect to the form page if the request method is not POST
    header("Location: FuelQuoteForm.html");
    exit;
}