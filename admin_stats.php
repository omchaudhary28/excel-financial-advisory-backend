<?php
require_once 'cors.php';
require_once 'db_connect.php';
require_once 'middleware_auth.php'; // For admin authentication

// Authenticate user and check if admin
$user = authenticate(true); // This will exit if not authenticated or not admin

try {
    // Total Users
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetchColumn();

    // Total Queries
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_queries FROM queries");
    $stmt->execute();
    $totalQueries = $stmt->fetchColumn();

    // New Users This Weekend (Saturday and Sunday)
    // Get the current date
    $currentDate = new DateTime();
    
    // Determine the start of the current week (Monday)
    $currentDate->modify('this week'); // This sets it to Monday of the current week

    // Calculate Saturday and Sunday of the current week
    $saturday = clone $currentDate;
    $saturday->modify('+5 days'); // Monday + 5 days = Saturday

    $sunday = clone $currentDate;
    $sunday->modify('+6 days'); // Monday + 6 days = Sunday

    // Format for database query (YYYY-MM-DD HH:MM:SS)
    $saturdayStart = $saturday->format('Y-m-D 00:00:00');
    $sundayEnd = $sunday->format('Y-m-D 23:59:59');

    $stmt = $pdo->prepare("SELECT COUNT(*) AS new_users_weekend FROM users WHERE created_at >= ? AND created_at <= ?");
    $stmt->execute([$saturdayStart, $sundayEnd]);
    $newUsersWeekend = $stmt->fetchColumn();

    echo json_encode([
        "success" => true,
        "data" => [
            "total_users" => $totalUsers,
            "total_queries" => $totalQueries,
            "new_users_weekend" => $newUsersWeekend,
            // "website_reach" is assumed to be total users for now
            "website_reach" => $totalUsers 
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}
