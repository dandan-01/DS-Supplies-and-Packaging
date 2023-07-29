<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Simple Content Management System for a DS Supplies and Packaging.

****************/

require('connect.php');

// SQL is written as a String.
$query = "SELECT packagingsupplies.*, images.filename AS image_filename 
          FROM packagingsupplies 
          LEFT JOIN images ON packagingsupplies.image_id = images.image_id
          ORDER BY packagingsupplies.product_id DESC";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 

// Check if there are any rows returned
if ($statement->rowCount() === 0) {
    $error = "<p>No products were found.</p>";
}

function shorten200($content, $maxLength = 200) {
    if (strlen($content) > $maxLength) {
        $excerpt = substr($content, 0, $maxLength) . '...';
        return $excerpt;
    } else {
        return $content;
    } //
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
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <nav id="adminnav">
        <h3>Welcome User!</h3>
        <a href="add_new_item.php">Add new item</a>
    </nav>

    <header>
        <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>Boxed N' Loaded</h1>
                <br>
                <h4><i>"Expertly Packed N' Safely Delivered to You!"</i></h4>
            </a>
        </div>

        <div class="searchNav">
            <input type="search" name="search" id="search" placeholder="Search...">
            <button type="submit"><i class="fa fa-search"></i></button>
        </div>

        <nav id="topright">
            <button>Register</button>
            <a href="index.php">Log In</a>
            <a href="new_post.php">Cart</a>
        </nav>
    </header>

    <nav id="productnav">
        <a href="index.php"><h2>HOME</h2></a>
        <a href=""><h2>BOXES</h2></a>
        <a href=""><h2>PAPERBAGS</h2></a>
        <a href=""><h2>SUPPLIES</h2></a>
    </nav>

    <section id="body">
    <h1>Viewing all products</h1>

    <div id="products">
        <ul>
        <?php foreach ($statement as $product): ?>
            <li>
                <div class="products-header">
                    <h2><a href="post.php?id=<?= $product['product_id']; ?>"><?= $product['product_name']; ?></a></h2>
                    <a href="edit.php?id=<?= $product['product_id']; ?>">edit</a>
                </div>

                <div class="products-timestamp">
                    <?= date("F d, Y, g:i a", strtotime($product['timestamp'])); ?>
                </div>

                <!-- Display the image if available -->
                <?php if ($product['image_id']): ?>
                    <div class="products-image">
                        <img src="imgs/<?= $product['image_filename']; ?>" alt="<?= $product['product_name']; ?>">
                    </div>
                <?php endif; ?>

                <div class="products-content">
                    <?= shorten200($product['product_description']); ?>
                    <?php if (strlen($product['product_description']) > 200): ?>
                        <a href="post.php?id=<?= $product['product_id']; ?>">Read Full Post</a>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</section>
</div>
    
</body>
</html>