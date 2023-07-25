<?php

/*******w******** 
    
    Name: Danilyn Sanchez
    Date: July 25, 2023
    Description: New post page.

****************/

require('connect.php');
require('authenticate.php');

if ($_POST && !empty($_POST['title']) && !empty($_POST['content'])) {
    //  Sanitize and filter user input
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    //  Build SQL query and bind to the above sanitized values.
    $query = "  INSERT INTO blogpost (title, content) 
                VALUES (:title, :content)";
    
    $statement = $db->prepare($query);
    
    //  Bind values to the parameters
    $statement->bindValue(':title', $title);
    $statement->bindValue(':content', $content);
    
    //  Execute the INSERT.
    //  execute() will check for possible SQL injection and remove if necessary
    if($statement->execute()) {
        echo "Success";
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="main.css">
  <title>DS Supplies and Packaging - New Blog Post</title>
</head>
<body>
    <div id="header">
    <div id="logotitle">
            <a href="index.php">
                <img src="imgs/box.jpg" alt="Box logo">
            </a>

            <a href="index.php">
                <h1>DS Supplies and Packaging</h1>
            </a>
        </div>

        <div id="nav">
            <a href="index.php">Home</a>
            <a href="new_post.php">New Post</a>
        </div>
    </div>

    <h1>New Blog Post</h1>

    <?php if (isset($error)): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="4" cols="50" required></textarea><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>