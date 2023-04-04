<?php
namespace MyNamespace;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
    
        return round($final_cost, 2); // Round the final cost to two decimal places
    }
    

    
}
