<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinic";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$notice = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inquiry = trim($_POST["inquiry"] ?? "");
    $name = trim($_POST["NAME"] ?? "");
    $email = trim($_POST["EMAIL"] ?? "");
    $message = trim($_POST["MESSAGE"] ?? "");

    if ($inquiry === "" || $name === "" || $email === "" || $message === "") {
        $notice = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notice = "Invalid email.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO messages (inquiry, NAME, EMAIL, MESSAGE) VALUES (?, ?, ?, ?)"
        );

        if ($stmt === false) {
            $notice = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssss", $inquiry, $name, $email, $message);
            if ($stmt->execute()) {
                $notice = "Message saved.";
            } else {
                $notice = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<?php if ($notice !== "") { echo "<p>" . htmlspecialchars($notice) . "</p>"; } ?>

