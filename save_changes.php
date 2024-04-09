<?php
session_start(); // Start the session

$file_path = $_POST['file_path'];
$dailyIntervalFilePathEAST = "/ftrallowlist/FTRAllowList/DailyIntervalFTRAllowListEAST.txt"; // Define the path for the DailyIntervalAllowListEAST
$dailyIntervalFilePathWEST = "/ftrallowlist/FTRAllowList/DailyIntervalFTRAllowListWEST.txt"; // Define the path for the DailyIntervalAllowListWEST
$changesMade = false; // Initialize a flag to check if any changes were made
$existingData = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$updatedData = [];
$newOrEdititedEntries = []; // Array to store new or edited entries

// Check if the file exists, if not create it
if (!file_exists($dailyIntervalFilePath)) {
    touch($dailyIntervalFilePath);
}

// Define the logging function
function appendToChangeLog($message)
{
    date_default_timezone_set('America/New_York'); // Set the timezone to Eastern Time
    $logFilePath = "/ftrallowlist/FTRAllowList/ChangeLog.txt";
    $timestamp = date('Y-m-d h:i:s a');
    $logMessage = $timestamp . " - " . $message . PHP_EOL;
    file_put_contents($logFilePath, $logMessage, FILE_APPEND);
}

// Handle deletions
$deletions = $_POST['delete'] ?? [];
foreach ($deletions as $deleteIndex) {
    if (isset($existingData[$deleteIndex])) {
        $changesMade = true;
        appendToChangeLog("Deleted: " . $existingData[$deleteIndex]);
    }
}

// Handle existing entries (edit or untouched)
if (isset($_POST['name']) && isset($_POST['ip'])) {
    foreach ($_POST['name'] as $index => $name) {
        if (!in_array($index, $deletions) && isset($existingData[$index])) {
            $ip = $_POST['ip'][$index];
            $currentEntry = $name . "," . $ip;
            // Check if the current line has been edited
            if ($existingData[$index] !== $name . "," . $ip) {
                $changesMade = true; // An edit was made
                appendToChangeLog("Updated from: " . $existingData[$index] . " to: " . $name . "," . $ip);
                $editedEntry = $name . "," . $ip;
                // Append the edited entry to the newOrEditedEntries array
                $newOrEditedEntries[] = $currentEntry;
            }
            $updatedData[] = $name . "," . $ip;
        } else {
        }
    }
}

// Handle new entries
if (isset($_POST['newName'], $_POST['newIp'])) {
    for ($i = 0; $i < count($_POST['newName']); $i++) {
        if (!empty($_POST['newName'][$i]) && !empty($_POST['newIp'][$i])) {
            $newName = trim($_POST['newName'][$i]);
            $newIp = trim($_POST['newIp'][$i]);
            if (!empty($newName) && !empty($newIp)) {
                $newEntry = $newName . "," . $newIp;
                $updatedData[] = $newName . "," . $newIp;
                $newOrEditedEntries[] = $newEntry; // Add the new entry to the newOrEditedEntries array
                $changesMade = true; // Change was made
                appendToChangeLog("Added: " . $newName . "," . $newIp);
            }
        }
    }
}

// Write the updated data back to the file
$file_handle = fopen($file_path, 'w');
foreach ($updatedData as $data) {
    fwrite($file_handle, $data . PHP_EOL);
}
fclose($file_handle);

// Write only new or edited entries to the second file
$file_handle_second = fopen($dailyIntervalFilePathEAST, 'a'); // Open for appending
foreach ($newOrEditedEntries as $entry) {
    fwrite($file_handle_second, $entry . PHP_EOL);
}
fclose($file_handle_second);

// Write only new or edited entries to the third file
$file_handle_third = fopen($dailyIntervalFilePathWEST, 'a'); // Open for appending
foreach ($newOrEditedEntries as $entry) {
    fwrite($file_handle_third, $entry . PHP_EOL);
}
fclose($file_handle_third);

// Set session messages based on whether changes were made
if ($changesMade) {
    $_SESSION['message'] = "Changes were made successfully.";
    // Define the path for script_file.txt
    $scriptFilePathEAST = "/ftrallowlist/FTRAllowList/script_fileEAST.txt";
    $scriptFilePathWEST = "/ftrallowlist/FTRAllowList/script_fileWEST.txt";
    // Check if the file exists, if not create it
    if (!file_exists($scriptFilePathEAST)) {
        touch($scriptFilePathEAST);
    }
    // Check if the second file exists, if not, create it
    if (!file_exists($scriptFilePathWEST)) {
        touch($scriptFilePathWEST);
    }
} else {
    $_SESSION['message'] = "No changes were made.";
}

header("Location: index.php"); // Redirect back to the main page
exit();
