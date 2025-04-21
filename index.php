<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Form Builder</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; }
        textarea { width: 90%; min-height: 100px; margin-bottom: 15px; padding: 10px; font-size: 1em; }
        button { padding: 10px 20px; font-size: 1em; cursor: pointer; }
        .form-field { margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 4px; }
        .form-field label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-field input[type="text"],
        .form-field input[type="email"],
        .form-field input[type="number"],
        .form-field input[type="date"],
        .form-field textarea,
        .form-field select {
            width: 95%; /* Adjust as needed */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        #generated-form-container { margin-top: 30px; border-top: 2px solid #ccc; padding-top: 20px;}
        pre { background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
    </style>
</head>
<body>

    <h1>AI Form Builder</h1>
    <p>Describe the form you want to create in plain English below.</p>
    <p>Example: "Create a contact form with fields for full name, email address (required), subject, and message (long text). Also include a dropdown for country selection with options USA, Canada, Mexico. Add radio buttons for preferred contact method: Email or Phone (default Email). Finally, add checkboxes for interests like Technology, Sports, and Music."</p>

    <form action="index.php" method="POST">
        <textarea name="form_description" placeholder="e.g., Create a simple contact form with name and email fields..." required><?php echo isset($_POST['form_description']) ? htmlspecialchars($_POST['form_description']) : ''; ?></textarea>
        <br>
        <button type="submit">Generate Form</button>
    </form>

    <div id="generated-form-container">
        <?php
        // PHP logic to handle the request and display the form will go here
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_description'])) {
            require 'form_generator.php'; // Include the generator logic
            $description = $_POST['form_description'];

            // --- AI Interaction ---
            // In a real app, you would call the AI API here
            $jsonResponse = callActualAI_API($description);

            // --- Simulation for Demonstration ---
            // For now, we'll simulate the AI response based on keywords
            // Replace this with your actual AI API call!
            // $jsonResponse = simulateAIResponse($description);
            echo "<h2>Simulated AI JSON Response:</h2>";
            echo "<pre>" . htmlspecialchars(json_encode(json_decode($jsonResponse), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . "</pre>"; // Show the JSON for debugging
            // --- End Simulation ---


            // --- Process Response and Render Form ---
            $formData = json_decode($jsonResponse, true); // Decode JSON into PHP array

            if (json_last_error() === JSON_ERROR_NONE && is_array($formData)) {
                echo "<h2>Generated Form:</h2>";

                echo renderFormFromData($formData); // Call the rendering function
                echo '-------------';

                echo "<h2>Save This Form</h2>";
                echo '<form action="save_form.php" method="POST">';
                // Pass the generated JSON definition
                echo '<input type="hidden" name="form_definition" value="' . htmlspecialchars($jsonResponse) . '">';
                echo '<div class="form-field">';
                echo '<label for="form_title">Form Title (Optional):</label>';
                echo '<input type="text" name="form_title" id="form_title" placeholder="e.g., Contact Us Form">';
                echo '</div>';
                echo '<div class="form-field">';
                echo '<button type="submit">Save Form</button>';
                echo '</div>';
                echo '</form>';

            } elseif ($jsonResponse) {
                 echo "<h2>Error:</h2>";
                 echo "<p>Failed to decode JSON response from AI.</p>";
                 echo "<pre>" . htmlspecialchars($jsonResponse) . "</pre>";
            } else {
                echo "<h2>Error:</h2>";
                echo "<p>No response received from AI simulation.</p>";
            }
        }
        ?>
    </div>

</body>
</html>