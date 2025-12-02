<?php

use App\Models\User;
use App\Models\Team;

echo "=== FIXING PROFILE PICTURE EXTENSIONS ===\n\n";

$fixedCount = 0;
$errors = [];

// Get all users and teams with profile pictures
$users = User::whereNotNull('userBanner')->select('id', 'name', 'userBanner')->get();
$teams = Team::whereNotNull('teamBanner')->select('id', 'teamName', 'teamBanner')->get();

// Check and fix user banners
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
                echo sprintf("Fixing User #%d (%s)\n", $user->id, $user->name);
                echo sprintf("  Current: %s (.%s)\n", $user->userBanner, $pathExtension);

                // Generate new filename with correct extension
                $pathInfo = pathinfo($user->userBanner);
                $newFileName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $expectedExt;
                $newStoragePath = storage_path('app/public/' . $newFileName);

                echo sprintf("  New: %s (.%s)\n", $newFileName, $expectedExt);

                try {
                    // Rename the physical file
                    if (rename($storagePath, $newStoragePath)) {
                        // Update database
                        $user->userBanner = $newFileName;
                        $user->save();

                        echo "  ✓ Successfully fixed!\n\n";
                        $fixedCount++;
                    } else {
                        $error = "Failed to rename file";
                        echo "  ✗ Error: $error\n\n";
                        $errors[] = "User #{$user->id}: $error";
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    echo "  ✗ Error: $error\n\n";
                    $errors[] = "User #{$user->id}: $error";
                }
            }
        }
    }
}

// Check and fix team banners
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
                echo sprintf("Fixing Team #%d (%s)\n", $team->id, $team->teamName);
                echo sprintf("  Current: %s (.%s)\n", $team->teamBanner, $pathExtension);

                // Generate new filename with correct extension
                $pathInfo = pathinfo($team->teamBanner);
                $newFileName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $expectedExt;
                $newStoragePath = storage_path('app/public/' . $newFileName);

                echo sprintf("  New: %s (.%s)\n", $newFileName, $expectedExt);

                try {
                    // Rename the physical file
                    if (rename($storagePath, $newStoragePath)) {
                        // Update database
                        $team->teamBanner = $newFileName;
                        $team->save();

                        echo "  ✓ Successfully fixed!\n\n";
                        $fixedCount++;
                    } else {
                        $error = "Failed to rename file";
                        echo "  ✗ Error: $error\n\n";
                        $errors[] = "Team #{$team->id}: $error";
                    }
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    echo "  ✗ Error: $error\n\n";
                    $errors[] = "Team #{$team->id}: $error";
                }
            }
        }
    }
}

echo "=== SUMMARY ===\n";
echo "Total files fixed: $fixedCount\n";
echo "Total errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}
