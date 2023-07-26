<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: Simple Content Management System for a DS Supplies and Packaging.

****************/

require('connect.php');

// SQL is written as a String.
$query = "  SELECT * 
            FROM blogpost 
            ORDER BY id DESC";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);

// Execution on the DB server is delayed until we execute().
$statement->execute(); 

// Check if there are any rows returned
if ($statement->rowCount() === 0) {
    $error = "<p>No blog posts found.</p>";
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
    <header>
        <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>Boxed N Loaded</h1>
                <br>
                <h4><i>"Expertly Packed, Safely Delivered to You!"</i></h4>
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

    <nav>
            <h2>Boxes</h2>
            <h2>Bags</h2>
            <h2>Supplies</h2>
    </nav>

    <section>
        <h2>Showing Recent Blog Posts</h2>

        <div id="blogs">
        <ul>
            <?php foreach ($statement as $blogpost): ?>
            <li>
                <div class="blog-header">
                    <h2><a href="post.php?id=<?= $blogpost['id']; ?>"><?= $blogpost['title']; ?></a></h2>
                    <a href="edit.php?id=<?= $blogpost['id']; ?>">edit</a>
                </div>

                <div class="blog-timestamp">
                    <?= date("F d, Y, g:i a", strtotime($blogpost['timestamp'])); ?>
                </div>

                <div class="blog-content">
                    <?= shorten200($blogpost['content']); ?>
                    <?php if (strlen($blogpost['content']) > 200): ?>
                            <a href="post.php?id=<?= $blogpost['id']; ?>">Read Full Post</a>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
    
</body>
</html>