<?php
require '../config/db.php';

$success = $error = $feature_success = $feature_error = "";

// Handle Package Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $property_id = $_POST['property_id'];
    $occupancy_type = $_POST['occupancy_type'];
    $package_type = $_POST['package_type'];
    $b2c_rate = $_POST['b2c_rate'];
    $extra_person_rate = $_POST['extra_person_rate'] ?? 0.00;

    try {
        $stmt = $pdo->prepare("INSERT INTO packages 
            (property_id, occupancy_type, package_type, b2c_rate, extra_person_rate) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$property_id, $occupancy_type, $package_type, $b2c_rate, $extra_person_rate]);
        $success = "Package added successfully!";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Feature Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_feature'])) {
    $name = trim($_POST['feature_name']);
    $description = trim($_POST['description']);
    $default_price = !empty($_POST['default_price']) ? floatval($_POST['default_price']) : 0.00;

    $check = $pdo->prepare("SELECT id FROM features WHERE name = ?");
    $check->execute([$name]);

    if ($check->rowCount() > 0) {
        $feature_error = "Feature already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO features (name, description, default_price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $default_price]);
        $feature_success = "Feature added successfully!";
    }
}

$properties = $pdo->query("SELECT id, name FROM properties ORDER BY name")->fetchAll();
$features = $pdo->query("SELECT id, name FROM features ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Package Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <h2 class="text-xl font-semibold mb-4">Package/Features</h2>
            <hr>
            <h3 class="text-xl font-semibold mb-4">Add New Feature</h3>
            <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>
            <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <form method="post">
                    <input type="hidden" name="add_package" value="1">

                    <div class="mb-4">
                        <label class="block font-medium">Property:</label>
                        <select name="property_id" required class="form-select w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option value="">Select Property</option>
                            <?php foreach ($properties as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Occupancy Type:</label>
                        <select name="occupancy_type" required class="form-select w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option value="">Select</option>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Package Type:</label>
                        <select name="package_type" required class="form-select w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option value="">Select</option>
                            <option value="CP">CP</option>
                            <option value="MAP">MAP</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">B2C Rate (₹):</label>
                        <input type="number" name="b2c_rate" step="0.01" required class="form-input w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Extra Person Rate (₹):</label>
                        <input type="number" name="extra_person_rate" step="0.01" required class="form-input w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <button type="submit" class="btn btn-success bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">Add Package</button>
                </form>
            </div>

            <hr>

            <h3 class="text-xl font-semibold mb-4">Add New Feature</h3>

            <?php if ($feature_success): ?><p style="color:green;"><?= $feature_success ?></p><?php endif; ?>
            <?php if ($feature_error): ?><p style="color:red;"><?= $feature_error ?></p><?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <form method="post">
                    <input type="hidden" name="add_feature" value="1">

                    <div class="mb-4">
                        <label class="block font-medium">Feature Name:</label>
                        <input type="text" name="feature_name" required class="form-input w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block font-medium">Description:</label>
                        <textarea name="description" rows="3" cols="50" class="form-textarea w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Default Price (optional):</label>
                        <input type="number" name="default_price" step="0.01" placeholder="0.00" class="form-input w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <button type="submit" class="btn btn-success bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">Add Feature</button>
                </form>
            </div>

        </main>
    </div>

    <script src="dashboard.js"></script>
</body>


</html>