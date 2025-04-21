<?php
// view_form.php

require_once __DIR__ . '/bootstrap.php';
require 'form_generator.php'; // Include renderFormFromData function

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No form ID provided.");
}

$uniqueId = $_GET['id']; // Basic retrieval, consider more validation

try {
    $stmt = $pdo->prepare("SELECT id, title, form_definition FROM forms WHERE unique_id = ?");
    $stmt->execute([$uniqueId]);
    $form = $stmt->fetch();

    if (!$form) {
        die("Form not found.");
    }

    $formDefinitionJson = $form['form_definition'];
    $formData = json_decode($formDefinitionJson, true); // Decode as array
    $formTitle = $form['title'];
    $formDbId = $form['id']; // Get the numeric DB ID for submission linking

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Failed to decode JSON for form unique_id: " . $uniqueId);
        die("Error loading form structure.");
    }

    // Prepare the URL for form submission
    $submitUrl = 'submit_form.php?form_db_id=' . $formDbId; // Pass DB ID for easier linking

} catch (\PDOException $e) {
    error_log("Error fetching form (unique_id: $uniqueId): " . $e->getMessage());
    die("An error occurred while loading the form.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $formTitle ? htmlspecialchars($formTitle) : 'Fill Form'; ?></title>
    <link rel="stylesheet" href="style.css"> <style>
        /* Paste the same CSS rules from index.php here if not using external file */
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: auto;}
        .form-field { margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 4px; }
        .form-field label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-field input[type="text"],
        .form-field input[type="email"],
        .form-field input[type="number"],
        .form-field input[type="date"],
        .form-field input[type="password"],
        .form-field textarea,
        .form-field select {
            width: 95%; /* Adjust as needed */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-field input[type="radio"],
        .form-field input[type="checkbox"] {
             margin-right: 5px;
        }
        .form-field .options label { /* Inline label for radio/checkbox */
            display: inline-block;
            margin-right: 15px;
            font-weight: normal;
        }
        .required-indicator { color: red; margin-left: 5px; }
        button[type="submit"] { padding: 12px 25px; font-size: 1.1em; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px; }
        button[type="submit"]:hover { background-color: #0056b3; }
        h1 { text-align: center; }
    </style>
</head>
<body>

    <h1><?php echo $formTitle ? htmlspecialchars($formTitle) : 'Please Fill Out This Form'; ?></h1>

    <form action="<?php echo htmlspecialchars($submitUrl); ?>" method="POST">
        <?php
        // Render the form using the function from form_generator.php
        if (function_exists('renderFormFromData')) {
            echo renderFormFromData($formData);
        } else {
            echo "<p>Error: Form rendering function not found.</p>";
        }
        ?>
        <div class="form-field">
            <button type="submit">Submit Answers</button>
        </div>
    </form>

</body>
</html>