<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/orderSummary.css">

    <title>Order Summary</title>

    <script src="components/nav-bar.js" defer></script>
    <script src="components/footer.js" defer></script>
    <script src="components/orderSummary.js" defer></script>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the values submitted in the form
            $companyName = $_POST["companyName"];
            $state = $_POST["state"];
            $city = $_POST["city"];
            $street = $_POST["street"];
            $fuelType = $_POST["fuelType"];
            $gallonsRequested = $_POST["gallonsRequested"];
            $fname = $_POST["fname"];
            $lname = $_POST["lname"];
            $custEmail = $_POST["custEmail"];
            $phone = $_POST["phone"];
            $paymentType = $_POST["paymentType"];

            // Print the values - Some are used for the order & some for pricing. All will be used for history.
            /*echo "<p>Company name: $companyName</p>";
            echo "<p>Email: $state</p>";
            echo "<p>Email: $city</p>";
            echo "<p>Email: $street</p>";
            echo "<p>Email: $fuelType</p>";
            echo "<p>Email: $gallonsRequested</p>";
            echo "<p>Email: $fname</p>";
            echo "<p>Email: $lname</p>";
            echo "<p>Email: $custEmail</p>";
            echo "<p>Email: $phone</p>";
            echo "<p>Email: $paymentType</p>";*/
        }
    ?>
</head>

<body>
    <form class="orderSummary" action="orderConfirmation.html">
    <div class="nav-bar" id="nav-bar"></div>
        <section name="summarySection">
            <h1><u>ORDER SUMMARY</u></h1>
            <p>Confirm your order:</p>
                <?php echo "<p>Ship to $companyName at: $street $city, $state</p>" ?>
                <?php echo "<p>$gallonsRequested gallons of $fuelType</p>" ?>
        </section>
        </section>
            <div class="priceEstimate" name="priceEstimate", id="priceEstimate">
                <ul class="priceEstimate">
                    <li class="priceEstimate"><?php echo "<p>Subtotal:</p>" ?></li>
                    <li class="priceEstimate"><?php echo "<p>Tax:</p>" ?></li>
                    <li class="priceEstimate"><?php echo "<p>Estimated Cost:</p>" ?></li>
                </ul>
            </div>
        </section>
        <section class="buttons">
            <input type="submit" value="Confirm" class="confirmButton" id="confirmButton" onclick="confirmOrder()">
            <input type="submit" formaction="index.htm" value="Cancel" class="cancelButton" id="cancelButton" onclick="goHome()">
        </section>
    </form>
</body>

<footer>
    <section>
        <div class="footer" id="footer"></div>
    </section>
</footer>
</html>