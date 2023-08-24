<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 21, 2023
    Description: Login authentication.

****************/

session_start();

define('ADMIN_LOGIN', 'wally');
define('ADMIN_PASSWORD', 'mypass');

function authenticateUser($email, $password) {
    global $db;
    // Check user credentials against the database
    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['user_email'] = $user['email'];
      $_SESSION['role'] = 'user'; // Store user role in session

      return true; // User login successful
  }
  return false; // Invalid login
}

function authenticateAdmin($username, $password) {
  if ($username === ADMIN_LOGIN && $password === ADMIN_PASSWORD) {
      $_SESSION['role'] = 'admin'; // Store admin role in session
      return true; // Admin login successful
  }
  return false; // Invalid admin login
}

// Handle the login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (authenticateAdmin($username, $password)) {
      // Admin authentication successful
    } elseif (authenticateUser($username, $password)) {
        // User authentication successful
    } else {
        $_SESSION['login_error'] = "Invalid username or password.";
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the URL
        header("Location: login.php");
        exit();
    }
}
?>