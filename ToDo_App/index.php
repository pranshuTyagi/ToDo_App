<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the database name from user input
if (isset($_POST['database_name'])) {
    $database_name = $_POST['database_name'];

    // Query to check if the database exists
    $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database_name'";
    $result = $conn->query($query);

    // If the database doesn't exist, create it
    if ($result->num_rows === 0) {
        $create_database_query = "CREATE DATABASE $database_name";
        if ($conn->query($create_database_query) === TRUE) {
            
            // Create the tasks table
            $create_table_query = "CREATE TABLE `$database_name`.`tasks` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `task` varchar(255) NOT NULL,
              `completed` tinyint(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              UNIQUE KEY `task` (`task`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            
            if ($conn->query($create_table_query) === TRUE) {
                echo '<div class="alert alert-success" role="alert">Database setup completed successfully.</div>';
                header("Location: ToDo_App\index.php?database_name=". urlencode($database_name));
                exit;
            } else {
                echo '<div class="alert alert-danger" role="alert">Error creating table: ' . $conn->error . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Error creating database: ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">Database already exists.</div>';
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ToDo App Setup</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Setup Your ToDo App</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="database_name">Enter Database Name:</label>
                                <input type="text" id="database_name" name="database_name" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Create Database</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
