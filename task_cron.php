<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Carbon\Carbon;
use PDO;
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        if (strlen($value) > 1 && substr($value, 0, 1) === '"' && substr($value, -1) === '"') {
            return substr($value, 1, -1);
        }
        return $value;
    }
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO(
        sprintf("mysql:host=%s;dbname=%s", env('DB_HOST'), env('DB_DATABASE')),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$twoMonthsAgo = Carbon::now()->subMonths(2)->format('Y-m-d H:i:s');
$pdo->exec("DELETE FROM tasks WHERE created_at < '$twoMonthsAgo'");
$pdo->exec("DELETE FROM monitored_scheduled_task_log_items WHERE created_at < '$twoMonthsAgo'");

$today = Carbon::now();
$twoWeeksAgo = $today->copy()->subWeeks(2)->format('Y-m-d');
$todayDate = $today->toDateString();
$todayTime = $today->toTimeString();

$launchEvents = $pdo->query("SELECT id FROM event_details WHERE DATE(launch_date) = '$todayDate' AND TIME(launch_time) <= '$todayTime'")->fetchAll(PDO::FETCH_COLUMN);
$endEvents = $pdo->query("SELECT id FROM event_details WHERE DATE(end_date) = '$todayDate'")->fetchAll(PDO::FETCH_COLUMN);
$registrationOverEvents = $pdo->query("SELECT id FROM event_details WHERE launch_date <= '$twoWeeksAgo'")->fetchAll(PDO::FETCH_COLUMN);

function createTasks($pdo, $eventIds, $taskName, $actionTime, $actionDate) {
    $stmt = $pdo->prepare("INSERT INTO tasks (event_id, task_name, action_time) VALUES (?, ?, ?)");
    $dateTime = date_create_from_format('Y-m-d H:i:s', $actionDate . ' ' . $actionTime);
    $formattedDateTime = $dateTime->format('Y-m-d H:i:s');

    foreach ($eventIds as $eventId) {
        $stmt->execute([$eventId, $taskName, $formattedDateTime]);
    }
}

createTasks($pdo, $launchEvents, 'launch', $todayTime, $todayDate);
createTasks($pdo, $endEvents, 'ended', $todayTime, $todayDate);
createTasks($pdo, $registrationOverEvents, 'registration_over', $todayTime, $todayDate);

echo "Cron job completed successfully.";