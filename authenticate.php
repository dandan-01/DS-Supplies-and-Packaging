<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 21, 2023
    Description: Login authentication.

****************/

session_start();

require('connect.php');

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
        header("Location: login.php"); 
        exit();
    } elseif (authenticateUser($username, $password)) {
        header("Location: login.php"); 
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: login.php");
        exit();
    }
}
?>