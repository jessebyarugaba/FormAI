<?php
// save_form.php

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['form_definition'])) {
    die("Invalid request.");
}

$formDefinitionJson = $_POST['form_definition'];
$formTitle = isset($_POST['form_title']) && !empty($_POST['form_title']) ? trim($_POST['form_title']) : null;

// Validate the JSON (basic check)
$formData = json_decode($formDefinitionJson);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid form definition received.");
}

// Generate a unique ID
$uniqueId = bin2hex(random_bytes(8)); // Generates a 16-character hex string

try {
    // Ensure unique_id is truly unique (highly unlikely collision, but good practice)
    // You might want a loop here to regenerate if a collision occurs in a high-traffic scenario
    $stmtCheck = $pdo->prepare("SELECT id FROM forms WHERE unique_id = ?");
    $stmtCheck->execute([$uniqueId]);
    if ($stmtCheck->fetch()) {
        // Collision occurred, handle it (e.g., regenerate, error out)
        die("Could not generate a unique ID. Please try again.");
    }


    $sql = "INSERT INTO forms (unique_id, title, form_definition) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$uniqueId, $formTitle, $formDefinitionJson]);

    // Generate the shareable link (adjust domain/path as needed)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['PHP_SELF']);
    // Ensure scriptDir doesn't double up slashes if it's '/'
    $basePath = rtrim($scriptDir, '/');
    $shareableLink = $protocol . $host . $basePath . '/view_form.php?id=' . $uniqueId;


    // Display success message and link
    echo "<!DOCTYPE html><html><head><title>Form Saved</title></head><body>";
    echo "<h1>Form Saved Successfully!</h1>";
    echo "<p>Your form can be accessed and filled out using the link below:</p>";
    echo '<p><a href="' . htmlspecialchars($shareableLink) . '">' . htmlspecialchars($shareableLink) . '</a></p>';
    echo '<p><button onclick="copyLink(\'' . htmlspecialchars($shareableLink) . '\')">Copy Link</button></p>';
    echo '<p><a href="index.php">Create another form</a></p>';
    echo '
    <script>
        function copyLink(link) {
            navigator.clipboard.writeText(link).then(function() {
                alert("Link copied to clipboard!");
            }, function(err) {
                alert("Could not copy link automatically. Please copy it manually.");
                console.error("Async: Could not copy text: ", err);
            });
        }
    </script>
    ';
    echo "</body></html>";

} catch (\PDOException $e) {
    error_log("Error saving form: " . $e->getMessage());
    die("An error occurred while saving the form. Please try again.");
}

?>