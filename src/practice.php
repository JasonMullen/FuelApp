<?php
use MyNamespace\GoogleMapsDistanceCalculator;
    $apiKey = 'AIzaSyDVaKfhI8-9bXexXD6Y3ODbeACWN6S8AXk';
    $db = new PDO('your_database_connection_string');
    $calculator = new GoogleMapsDistanceCalculator($apiKey, $db);
    
    $startingAddress = '123 Main St, New York, NY';
    $destinationAddress = '456 Elm St, Los Angeles, CA';
    
    $distance = $calculator->getDistanceToDestination($startingAddress, $destinationAddress);
    echo "Distance: {$distance} miles\n";
    
    $order = new Order($db); // assuming you have an Order class that deals with orders
    $fuel_quote = new FuelQuote($db); // assuming you have a FuelQuote class that deals with fuel quotes
    
    $final_cost = $calculator->calculate_final_cost($order, $fuel_quote);
    echo "Final cost: $final_cost\n";
    
?>