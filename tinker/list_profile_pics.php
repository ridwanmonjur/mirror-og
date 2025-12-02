<?php

use App\Models\User;
use App\Models\Team;

// List all user profile pictures
echo "=== USER PROFILE PICTURES ===\n";
echo "Total Users: " . User::count() . "\n";
echo "Users with banners: " . User::whereNotNull('userBanner')->count() . "\n\n";

$users = User::whereNotNull('userBanner')->select('id', 'name', 'userBanner')->get();

echo "User ID | User Name | Banner Path\n";
echo str_repeat('-', 100) . "\n";
foreach ($users as $user) {
    echo sprintf("%-7s | %-30s | %s\n", $user->id, substr($user->name, 0, 30), $user->userBanner);
}

echo "\n=== TEAM PROFILE PICTURES ===\n";
echo "Total Teams: " . Team::count() . "\n";
echo "Teams with banners: " . Team::whereNotNull('teamBanner')->count() . "\n\n";

$teams = Team::whereNotNull('teamBanner')->select('id', 'teamName', 'teamBanner')->get();

echo "Team ID | Team Name | Banner Path\n";
echo str_repeat('-', 100) . "\n";
foreach ($teams as $team) {
    echo sprintf("%-7s | %-30s | %s\n", $team->id, substr($team->teamName, 0, 30), $team->teamBanner);
}

echo "\n=== CHECKING FILE EXTENSIONS ===\n";
$wrongExtensions = [];

// Check user banners
foreach ($users as $user) {
    if ($user->userBanner) {
        $storagePath = storage_path('app/public/' . $user->userBanner);
        if (file_exists($storagePath)) {
            // Get actual file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $storagePath);
            finfo_close($finfo);

            // Get extension from path
            $pathExtension = strtolower(pathinfo($user->userBanner, PATHINFO_EXTENSION));

            // Map mime types to expected extensions
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            $expectedExt = $mimeToExt[$mimeType] ?? null;

            if ($expectedExt && $pathExtension !== $expectedExt && $pathExtension !== 'jpeg') {
                $wrongExtensions[] = [
                    'type' => 'user',
                    'id' => $user->id,
                    'name' => $user->name,
                    'path' => $user->userBanner,
                    'current_ext' => $pathExtension,
                    'correct_ext' => $expectedExt,
                    'mime_type' => $mimeType,
                ];
                echo sprintf("WRONG: User #%d (%s) - File: %s | Current: .%s | Should be: .%s (MIME: %s)\n",
                    $user->id, substr($user->name, 0, 20), $user->userBanner, $pathExtension, $expectedExt, $mimeType);
            }
        } else {
            echo sprintf("MISSING FILE: User #%d (%s) - File: %s\n", $user->id, substr($user->name, 0, 20), $user->userBanner);
        }
    }
}

// Check team banners
foreach ($teams as $team) {
    if ($team->teamBanner) {
        $storagePath = storage_path('app/public/' . $team->teamBanner);
        if (file_exists($storagePath)) {
            // Get actual file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $storagePath);
            finfo_close($finfo);

            // Get extension from path
            $pathExtension = strtolower(pathinfo($team->teamBanner, PATHINFO_EXTENSION));

            // Map mime types to expected extensions
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            $expectedExt = $mimeToExt[$mimeType] ?? null;

            if ($expectedExt && $pathExtension !== $expectedExt && $pathExtension !== 'jpeg') {
                $wrongExtensions[] = [
                    'type' => 'team',
                    'id' => $team->id,
                    'name' => $team->teamName,
                    'path' => $team->teamBanner,
                    'current_ext' => $pathExtension,
                    'correct_ext' => $expectedExt,
                    'mime_type' => $mimeType,
                ];
                echo sprintf("WRONG: Team #%d (%s) - File: %s | Current: .%s | Should be: .%s (MIME: %s)\n",
                    $team->id, substr($team->teamName, 0, 20), $team->teamBanner, $pathExtension, $expectedExt, $mimeType);
            }
        } else {
            echo sprintf("MISSING FILE: Team #%d (%s) - File: %s\n", $team->id, substr($team->teamName, 0, 20), $team->teamBanner);
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total profile pictures: " . ($users->count() + $teams->count()) . "\n";
echo "Pictures with wrong extensions: " . count($wrongExtensions) . "\n";
