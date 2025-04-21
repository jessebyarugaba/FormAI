<?php
// submit_form.php

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['form_db_id'])) {
    die("Invalid submission.");
}

$formDbId = filter_var($_GET['form_db_id'], FILTER_VALIDATE_INT);
if ($formDbId === false) {
    die("Invalid form identifier.");
}

// Optional: Verify the form still exists
$stmtCheck = $pdo->prepare("SELECT id FROM forms WHERE id = ?");
$stmtCheck->execute([$formDbId]);
if (!$stmtCheck->fetch()) {
    die("Cannot submit to a non-existent form.");
}


// Get submitter IP (optional, consider privacy)
$submitterIp = $_SERVER['REMOTE_ADDR'] ?? null;

try {
    // Start transaction
    $pdo->beginTransaction();

    // 1. Insert into submissions table
    $sqlSub = "INSERT INTO submissions (form_id, submitter_ip) VALUES (?, ?)";
    $stmtSub = $pdo->prepare($sqlSub);
    $stmtSub->execute([$formDbId, $submitterIp]);
    $submissionId = $pdo->lastInsertId(); // Get the ID of the new submission

    // 2. Insert answers into answers table
    $sqlAns = "INSERT INTO answers (submission_id, field_name, field_value) VALUES (?, ?, ?)";
    $stmtAns = $pdo->prepare($sqlAns);

    foreach ($_POST as $fieldName => $fieldValue) {
        // Skip any fields not part of the actual form data if needed
        // (e.g., if you added hidden fields for CSRF later)

        $valueToStore = null;
        if (is_array($fieldValue)) {
            // Handle multi-select checkboxes or potentially other array inputs
            // Store as JSON string
            $valueToStore = json_encode($fieldValue);
        } else {
            // Store simple string value
            $valueToStore = trim($fieldValue);
        }

        // Execute prepared statement for each answer
        $stmtAns->execute([$submissionId, $fieldName, $valueToStore]);
    }

    // Commit transaction
    $pdo->commit();

    // Redirect to a thank you page or display message
    // header('Location: thank_you.php'); // Optional: Create thank_you.php
    echo "<!DOCTYPE html><html><head><title>Submission Received</title></head><body>";
    echo "<h1>Thank You!</h1><p>Your submission has been received.</p>";
     echo '<p><a href="view_form.php?id=' . htmlspecialchars($_GET['id'] ?? '') . '">Fill again</a> (For testing)</p>'; // Add link back for testing
    echo "</body></html>";
    exit;


} catch (\PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error saving submission for form ID $formDbId: " . $e->getMessage());
    die("An error occurred while submitting your answers. Please try again.");
} catch (\Exception $e) {
     // Catch other potential errors
     if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("General error during submission for form ID $formDbId: " . $e->getMessage());
    die("An unexpected error occurred. Please try again.");
}

?>