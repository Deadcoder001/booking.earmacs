<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Property Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <h1 class="text-3xl font-bold mb-6">Property Management Dashboard</h1>

            <!-- KPI Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-sm text-gray-500">Total Properties</h2>
                    <p id="total-properties" class="text-2xl font-bold text-indigo-600">0</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-sm text-gray-500">Total Rooms</h2>
                    <p id="total-rooms" class="text-2xl font-bold text-indigo-600">0</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-sm text-gray-500">Total Revenue</h2>
                    <p id="total-revenue" class="text-2xl font-bold text-indigo-600">0.00</p>
                </div>
            </div>

            <!-- Property Summary -->
            <h2 class="text-xl font-semibold mb-4">Property Summary</h2>
            <div id="property-summary-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                <!-- Cards injected here -->
            </div>

            <!-- Hidden Template for Property Summary -->
            <template id="property-summary-template">
                <div class="bg-white p-4 rounded-lg shadow property-summary-item cursor-pointer hover:bg-indigo-50 transition">
                    <h4 class="text-lg font-semibold mb-1">[Property Name]</h4>
                    <p class="text-sm text-gray-500">Rooms: <span class="rooms-available">0</span></p>
                    <p class="text-sm text-gray-500">Revenue: <span class="property-revenue">0.00</span></p>
                    <ul class="room-revenue-list mt-2 text-sm text-gray-600 space-y-1"></ul>
                </div>
            </template>

            <!-- Booking and Calendar Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Booking Table -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Recent Bookings</h3>
                        <select id="property-select" class="border rounded px-2 py-1 text-sm">
                            <option value="">Select Property</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-700 border">
                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-4 py-2 border">Name</th>
                                    <th class="px-4 py-2 border">Phone</th>
                                    <th class="px-4 py-2 border">Payment</th>
                                    <th class="px-4 py-2 border">Method</th>
                                    <th class="px-4 py-2 border">Room(s)</th>
                                </tr>
                            </thead>
                            <tbody id="booking-details-body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Room Booking Calendar</h3>
                    <div id="calendar" class="grid grid-cols-7 gap-2 text-center text-sm"></div>
                </div>
            </div>
        </main>
    </div>

    <script src="dashboard.js"></script>
</body>

</html>