<?php
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = $_GET['database_name'];

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to add task to the database only if it doesn't already exist
function addTask($task, $conn) {
    // Check if task already exists
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE task = ?");
    $stmt->bind_param("s", $task);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    if ($result->num_rows > 0) {
        // Task already exists, handle the case (e.g., display an error message)
        echo '<script>alert("Task already exists!");</script>';
    } else {
        // Task doesn't exist, insert it into the database
        $stmt = $conn->prepare("INSERT INTO tasks (task) VALUES (?)");
        $stmt->bind_param("s", $task);
        $stmt->execute();
        $stmt->close();
    }
}

// Function to get all tasks from the database
function getTasks($conn) {
    $sql = "SELECT * FROM tasks";
    $result = $conn->query($sql);
    $tasks = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    }
    return $tasks;
}

// Function to delete task from the database
function deleteTask($id, $conn) {
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Function to mark task as completed in the database
function markTaskCompleted($id, $conn) {
    $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Handling post requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['task']) && !empty($_POST['task'])) {
        $task = $_POST['task'];
        addTask($task, $conn);
    }
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        deleteTask($delete_id, $conn);
    }
    if (isset($_POST['complete_id'])) {
        $complete_id = $_POST['complete_id'];
        markTaskCompleted($complete_id, $conn);
    }
}

// Display all tasks
$tasks = getTasks($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Bootstrap design -->
    <div class="container">
        <h1 class="mb-4 text-center">ToDo List</h1>
        <form method="post">
         <div class="row">
            <div class="col">
                <input type="text"class="form-control" name="task" placeholder="Enter task">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Add Task</button>
            </div>
        </div>
        </form>
        <div class="row">
            <div class="col">
            <table class="table">
            <thead>
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><input type="checkbox" <?php echo $task['completed'] ? 'checked' : ''; ?> onchange="this.form.submit()" name="completed_id" value="<?php echo $task['id']; ?>" disabled>
               <?php echo $task['task']; ?></span></td>
               <td> <span><?php  echo $task['completed'] ? 'Completed' : 'Not Yet'; ?></span></td>
               <td> <form method="post" style="display: inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                </form>
                <!-- Add Complete button -->
                <?php if (!$task['completed']): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="complete_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn btn-success">Complete</button>
                                </form></td>
                            <?php endif; ?>
                </tr>
        <?php endforeach; ?>
                </table>
        </div>
        </div>
    </div>
    <!-- End here -->
    
    
    
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
