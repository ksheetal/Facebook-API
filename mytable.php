  <?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "myDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE Myposts (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
post VARCHAR(30) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Myposts created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
