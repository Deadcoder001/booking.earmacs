<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Feature</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h2>Add New Feature</h2>

    <form method="post" action="">
        <label>Feature Name:</label>
        <input type="text" name="name" required><br><br>
        <label for="description">Description</label>
        <textarea name="description" rows="4" cols="50"></textarea><br><br>
        <label>Default Price (optional):</label>
        <input type="number" name="default_price" step="0.01" placeholder="0.00"><br><br>

        <button type="submit" name="submit">Add Feature</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $name = trim($_POST['name']);
        $default_price = !empty($_POST['default_price']) ? floatval($_POST['default_price']) : 0.00;

        // Prevent duplicates
        $check = $pdo->prepare("SELECT id FROM features WHERE name = ?");
        $check->execute([$name]);

        if ($check->rowCount() > 0) {
            echo "<p style='color:red;'>Feature already exists.</p>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO features (name, description, default_price) VALUES (?,?, ?)");
            $stmt->execute([$name, $description, $default_price]);
            echo "<p style='color:green;'>Feature added successfully!</p>";
        }
    }
    ?>
</body>

</html>