<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 21, 2023
    Description: Register user page.

****************/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $error = "Error: Passwords do not match. Please try again.";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Store user information in a database or file
        $successMessage = "Welcome you have been registered successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Boxed N Loaded</title>
</head>
<body>
    <nav id="adminnav">
        <h3>Welcome User!</h3>
        <a href="add_new_item.php">Add new item</a>
        <a href="categories.php">Create/Update Category</a>
    </nav>

    <header>
        <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>Boxed N' Loaded</h1>
                <br>
                <h4><i>"Your go-to for Shipping Supplies!"</i></h4>
            </a>
        </div>

        <div class="searchNav">
            <form action="search_results.php" method="GET">
                <ul>
                    <li>
                        <input type="search" name="search_query" id="search" placeholder="Search...">

                        <!-- Search using specific categories -->
                        <select name="category">
                            <option value="">All</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id']; ?>"><?= $category['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>

                    <li>
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </li>
                </ul>
            </form>
        </div>

        <nav id="topright">
            <a href="register_user.php">Register</a>
            <a href="index.php">Log In</a>
            <a href="new_post.php">Cart</a>
        </nav>
    </header>

    <nav id="productnav">
        <a href="index.php"><h2>HOME</h2></a>
        <a href="category_header_page.php?category_name=Boxes"><h2>BOXES</h2></a>
        <a href="category_header_page.php?category_name=Paperbags"><h2>PAPERBAGS</h2></a>
        <a href="category_header_page.php?category_name=Supplies"><h2>SUPPLIES</h2></a>
    </nav>

    <section class="center">
        <ul>
            <li>
                <h2>User Registration</h2>
            </li>

            <li>
                <?php if (isset($error)): ?>
                    <p><?= $error ?></p>
                <?php endif; ?>
            </li>

            <li>
                <?php if (isset($successMessage)): ?>
                    <p><?= $successMessage ?></p>
                <?php else: ?>
                    <form action="" method="post">
                        <label for="email">Email:</label>
                        <input type="email" name="email" required><br><br>

                        <label for="password">Password:</label>
                        <input type="password" name="password" required><br><br>

                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" required><br><br>

                        <input type="submit" value="Register">
                    </form>
                <?php endif; ?> 
            </li>
        </ul>
    </section>
    
</body>
</html>