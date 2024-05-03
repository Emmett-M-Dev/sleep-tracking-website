<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "emurray46";

    // Create connection
    $conn = new mysqli($servername, $db_username, $db_password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    // Trim to remove whitespace and escape to prevent SQL injection
    $username = trim($conn->real_escape_string($_POST['username']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = trim($conn->real_escape_string($_POST['password']));

    // Check for empty fields
    if (empty($username) || empty($email) || empty($password)) {
        $errorMsg = "All fields are required.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close(); // Close this statement as soon as we're done with it

        if ($result->num_rows > 0) {
            $errorMsg = "Username or Email already exists.";
        } else {
            // Insert new user
            
            $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $username, $email, $password);
            if ($insert_stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $errorMsg = "Error: " . $insert_stmt->error;
            }
            $insert_stmt->close(); // Close this statement only if it's been initialized
        }
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
   


    <!-- Include Tailwind CSS from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        /* fade-in animation */
        .container {
            opacity: 0;
            transform: translateX(-4rem);
            transition: opacity 1s ease-in-out, transform 0.5s ease-in-out;
        }

        .container.loaded {
            opacity: 1;
            transform: translateX(0);
        }
        body {
            margin: 0;
            padding: 0;
            background-image: url('includes/images/MoonSkyBackground.webp');
            background-attachment: fixed; /* This makes the background image fixed */
            background-position: center; /* Centers the image in the viewport */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-size: cover; /* Covers the entire viewport */
            
        }
        .error-msg {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>

</head>
<body class="bg-purple-900">
<div class="container mx-auto mt-8 p-4">
        <div class="bg-navy-900 text-white p-8 rounded-lg shadow-xl max-w-md mx-auto">
        
            <h2 class="text-2xl mb-4">Registration</h2>
            <?php if (!empty($errorMsg)): ?>
                <div class="error-msg">
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>
            <form method="post" action="" class="space-y-4">
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="Enter your username" class="rounded p-2 w-full text-black">
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" class="rounded p-2 w-full text-black">
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" class="rounded p-2 w-full text-black">
                </div>
                <div class="input-group">
                    <button type="submit" name="register" id="registerButton" class="bg-blue-500 text-white rounded p-2">Register</button>
                </div>
                <p class="text-center">or</p>
                <div class="input-group">
                    <a href="login.php" class="bg-blue-500 text-white rounded p-2">Log In</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for fade-in animation -->
    <script>
         document.addEventListener("DOMContentLoaded", function () {
            const container = document.querySelector('.container');
            container.classList.add('loaded');
        });
    </script>
</body>
</html>