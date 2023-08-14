<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: Aug 8, 2023
    Description: Create/view category page.

****************/

require('connect.php');
require('authenticate.php');

// Needs to be instantiated as an array to use in foreach loop
$categories = [];

if ($_POST && !empty($_POST['category_name'])) {
    // Sanitize and filter user input
    $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Build SQL query and bind to the above sanitized values.
    $query = "INSERT INTO categories (category_name) 
          VALUES (:category_name)";

    $statement = $db->prepare($query);

    // Bind values to the parameters
    $statement->bindValue(':category_name', $category_name);

    // Execute the INSERT.
    // execute() will check for possible SQL injection and remove if necessary
    if ($statement->execute()) {
        echo "Success";
        header('Location: categories.php');
        exit;
    }
}

// Fetch current categories from database
$categoriesQuery = "SELECT * FROM categories";
$categoriesStatement = $db->query($categoriesQuery);
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- Remember that alternative syntax is good and html inside php is bad -->
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
            <button>Register</button>
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

    <section id="create-category">
        <h1>Create Category</h1>

        <form class="center" method="POST" >
            <ul>
                <li>
                    <label for="category_name">Category name:</label><br>
                    <input type="text" id="category_name" name="category_name" required size="50"><br>
                </li>
            
                <li>
                    <input id="submitbtn" type="submit" value="Submit">
                </li>
        </form>
    </section>

    <section id="current-categories" class="padding-bottom">
        <ul>
            <li>
                <h1>Current Categories</h1>
            </li>

            <li>
                <table class="center">
                    <tbody>
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Update</th>
                        </tr>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['category_id']; ?></td>
                                <td><?= $category['category_name']; ?></td>
                                <td><a href="edit_category.php?category_id=<?= $category['category_id']; ?>">Edit/Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="center"><i>Note: There will be gaps between IDs due to deletion testing<i></p>
            </li>
        </ul>
    </section>
</body>
</html>