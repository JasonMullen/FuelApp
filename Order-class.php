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
