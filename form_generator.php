<?php

require 'vendor/autoload.php'; // Ensure you have the OpenAI PHP SDK installed
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

require_once __DIR__ . '/bootstrap.php';

/**
 * Constructs the prompt for the AI model.
 *
 * @param string $userDescription The user's natural language description.
 * @return string The full prompt for the AI.
 */
function buildAIPrompt(string $userDescription): string
{
    // Provide clear instructions and the desired format
    $system_instruction = '
    You are an AI assistant that generates form structures in JSON format based on user descriptions.
    The user will describe a form, and you must return a JSON array representing the form fields.

    Each object in the JSON array should represent one form field and have the following properties:
    - "type": (string) The type of input field. Supported types: "text", "email", "number", "date", "textarea", "select", "radio", "checkbox". Infer the best type.
    - "name": (string) A machine-readable name for the field (use lowercase_snake_case). Generate a relevant name.
    - "label": (string) A human-readable label for the field. Generate a relevant label.
    - "placeholder": (string|null) Placeholder text for text-based inputs, or null otherwise.
    - "required": (boolean) True if the field is mandatory, false otherwise. Infer from the description (e.g., "required", "must provide"). Default to false if not specified.
    - "value": (string|null) A default value for the field (e.g., for pre-selected radio/select, or default text). Null if no default.
    - "options": (array|null) An array of strings or objects (with "value" and "label" keys) for "select", "radio", or "checkbox" types. Null for other types. Generate options based on the description. For checkboxes, ensure the "name" often ends with "[]" like "interests[]". Here is a sample of a response: [
      {
        "type": "text", // text, textarea, select, radio, checkbox, email, number, date, etc.
        "name": "user_name", // machine-readable name for form submission (AI should generate this)
        "label": "Full Name", // human-readable label (AI should generate this)
        "placeholder": "Enter your full name", // placeholder text (AI should generate this)
        "required": true, // boolean indicating if the field is mandatory
        "value": "", // default value (optional)
        "options": null // only used for select, radio, checkbox (array of strings or key/value pairs)
      },
      {
        "type": "email",
        "name": "user_email",
        "label": "Email Address",
        "placeholder": "your.email@example.com",
        "required": true,
        "value": "",
        "options": null
      },
      {
        "type": "textarea",
        "name": "message",
        "label": "Your Message",
        "placeholder": "Type your message here...",
        "required": false,
        "value": "",
        "options": null
      },
      {
        "type": "select",
        "name": "country",
        "label": "Country",
        "required": true,
        "value": "USA", // Default selected value
        "options": [ // Can be simple array
            "USA",
            "Canada",
            "Mexico",
            "United Kingdom"
        ]
      },
      {
        "type": "radio",
        "name": "contact_method",
        "label": "Preferred Contact Method",
        "required": true,
        "value": "email", // Default checked value
        "options": [ // Or array of objects for more control (e.g., distinct value/label)
            {"value": "email", "label": "Email"},
            {"value": "phone", "label": "Phone"}
        ]
      },
      {
        "type": "checkbox",
        "name": "interests", // Often ends with [] for PHP processing: interests[]
        "label": "Interests (Select multiple)",
        "required": false,
        "value": null,
        "options": [
            "Technology",
            "Sports",
            "Music"
        ]
      }
    ]
    Analyze the following user description and generate ONLY the JSON array according to the structure specified above. Do not include any introductory text or explanations outside the JSON. Do not include comments in the code.';

    return $system_instruction;
}


/**
 * Simulates calling an AI API.
 * **Replace this function with your actual API call logic.**
 *
 * @param string $userDescription
 * @return string|false A JSON string representing the form structure, or false on failure.
 */
function simulateAIResponse(string $userDescription): string|false
{
    // Basic keyword-based simulation (VERY LIMITED)
    // A real AI would understand context much better.
    $descriptionLower = strtolower($userDescription);

    if (str_contains($descriptionLower, 'contact form') && str_contains($descriptionLower, 'country') && str_contains($descriptionLower, 'interests')) {
         // Corresponds to the example prompt in index.php
         return <<<JSON
[
  {
    "type": "text",
    "name": "full_name",
    "label": "Full Name",
    "placeholder": "Enter your full name",
    "required": true,
    "value": null,
    "options": null
  },
  {
    "type": "email",
    "name": "user_email",
    "label": "Email Address",
    "placeholder": "your.email@example.com",
    "required": true,
    "value": null,
    "options": null
  },
  {
    "type": "text",
    "name": "subject",
    "label": "Subject",
    "placeholder": "Subject of your message",
    "required": false,
    "value": null,
    "options": null
  },
  {
    "type": "textarea",
    "name": "message",
    "label": "Message",
    "placeholder": "Type your message here...",
    "required": true,
    "value": null,
    "options": null
  },
  {
    "type": "select",
    "name": "country",
    "label": "Country",
    "required": true,
    "value": null,
    "options": [
        "USA",
        "Canada",
        "Mexico"
    ]
  },
  {
    "type": "radio",
    "name": "contact_method",
    "label": "Preferred Contact Method",
    "required": true,
    "value": "email",
    "options": [
        {"value": "email", "label": "Email"},
        {"value": "phone", "label": "Phone"}
    ]
  },
  {
    "type": "checkbox",
    "name": "interests[]",
    "label": "Interests",
    "required": false,
    "value": null,
    "options": [
       "Technology",
       "Sports",
       "Music"
    ]
  }
]
JSON;
    } elseif (str_contains($descriptionLower, 'login form')) {
        return <<<JSON
[
  {
    "type": "text",
    "name": "username",
    "label": "Username",
    "placeholder": "Enter your username",
    "required": true,
    "value": null,
    "options": null
  },
  {
    "type": "password",
    "name": "password",
    "label": "Password",
    "placeholder": "Enter your password",
    "required": true,
    "value": null,
    "options": null
  }
]
JSON;
    } else {
        // Default simple form if no keywords match
        return <<<JSON
[
  {
    "type": "text",
    "name": "field_1",
    "label": "Field 1",
    "placeholder": "Enter value for Field 1",
    "required": false,
    "value": null,
    "options": null
  }
]
JSON;
    }
}

/**
 * !! IMPORTANT: Actual AI API Call Function (Example structure using cURL) !!
 * Replace simulateAIResponse with calls to this function in a real application.
 *
 * @param string $userDescription
 * @return string|false JSON string response or false on error
 */
function callActualAI_API(string $userDescription): string|false
{
    $systemPrompt = buildAIPrompt($userDescription); // Assuming buildAIPrompt returns the system instructions

    // --- Choose a suitable, available model ---
    // $modelToUse = 'google/gemini-pro';
    $modelToUse = 'meta-llama/llama-4-maverick:free';

    $client = new Client([
        'timeout' => 60.0, // Set a timeout (e.g., 60 seconds)
    ]);

    try {
        $response = $client->post($_ENV['OPENROUTER_API_ENDPOINT'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $_ENV['OPENROUTER_API_KEY'],
                'Content-Type' => 'application/json',
                // OpenRouter specific recommended header (helps them track usage)
                // 'HTTP-Referer' => 'YOUR_APP_URL_OR_NAME', // Optional but recommended
                // 'X-Title' => 'YOUR_APP_NAME' // Optional but recommended
            ],
            'json' => [
                'model' => $modelToUse, // Use the chosen available model
                'messages' => [
                    // System prompt provides instructions on the JSON format
                    ['role' => 'system', 'content' => $systemPrompt],
                    // User prompt is the actual request
                    ['role' => 'user', 'content' => $userDescription] // Sending the raw description might be better
                ],
                // Optional: Add parameters like temperature, max_tokens if needed
                // 'temperature' => 0.7,
                // 'max_tokens' => 1000,
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        // --- Correct Parsing for OpenRouter/OpenAI structure ---
        if (isset($body['choices'][0]['message']['content'])) {
            $jsonText = $body['choices'][0]['message']['content'];

            // Attempt to clean up potential markdown fences ```json ... ```
            $jsonText = trim($jsonText);
            if (str_starts_with($jsonText, '```json')) {
                $jsonText = substr($jsonText, 7);
            }
            if (str_ends_with($jsonText, '```')) {
                $jsonText = substr($jsonText, 0, -3);
            }
            return trim($jsonText); // Return the cleaned JSON string
        } else {
            // Log the unexpected response structure for debugging
            error_log("AI API Response format unexpected. Body: " . json_encode($body));
            return false;
        }

    } catch (RequestException $e) {
        // Log Guzzle exceptions (includes HTTP errors like 4xx, 5xx)
        error_log("AI API Call Guzzle Error: " . $e->getMessage());
        if ($e->hasResponse()) {
            error_log("Response Body: " . $e->getResponse()->getBody()->getContents());
        }
        return false;
    } catch (\Exception $e) {
        // Log other general exceptions
        error_log("AI API Call General Error: " . $e->getMessage());
        return false;
    }
}


/**
 * Renders HTML form elements from the structured data array.
 *
 * @param array $formData Array of form field objects.
 * @return string The generated HTML string.
 */
function renderFormFromData(array $formData): string
{
    $html = '';
    foreach ($formData as $field) {
        // Basic validation of expected keys
        if (!isset($field['type'], $field['name'], $field['label'])) {
             //_log("Skipping invalid field structure: " . print_r($field, true));
            continue;
        }

        // Sanitize output data to prevent XSS
        $type = htmlspecialchars($field['type']);
        $name = htmlspecialchars($field['name']);
        $label = htmlspecialchars($field['label']);
        $placeholder = isset($field['placeholder']) ? htmlspecialchars($field['placeholder']) : '';
        $value = isset($field['value']) ? htmlspecialchars($field['value']) : '';
        $required = isset($field['required']) && $field['required'] === true;
        $options = isset($field['options']) && is_array($field['options']) ? $field['options'] : [];

        $html .= '<div class="form-field">';
        $fieldId = 'field_' . preg_replace('/[^a-zA-Z0-9_]/', '', $name); // Generate a basic ID

        // Add label
        $html .= '<label for="' . $fieldId . '">' . $label;
        if ($required) {
            $html .= '<span class="required-indicator">*</span>';
        }
        $html .= '</label>';

        // Add required attribute string if needed
        $requiredAttr = $required ? ' required' : '';

        // Generate field based on type
        switch ($type) {
            case 'textarea':
                $html .= '<textarea name="' . $name . '" id="' . $fieldId . '" placeholder="' . $placeholder . '"' . $requiredAttr . '>' . $value . '</textarea>';
                break;

            case 'select':
                $html .= '<select name="' . $name . '" id="' . $fieldId . '"' . $requiredAttr . '>';
                if ($placeholder && !$required) { // Add a non-selectable placeholder option if not required
                     $html .= '<option value="" disabled selected>' . $placeholder . '</option>';
                } elseif ($placeholder) {
                     $html .= '<option value="" disabled ' . (empty($value) ? 'selected' : '') . '>' . $placeholder . '</option>';
                }

                foreach ($options as $option) {
                    $optionValue = '';
                    $optionLabel = '';
                    if (is_array($option) && isset($option['value'], $option['label'])) {
                        $optionValue = htmlspecialchars($option['value']);
                        $optionLabel = htmlspecialchars($option['label']);
                    } elseif (is_string($option)) {
                        $optionValue = htmlspecialchars($option);
                        $optionLabel = htmlspecialchars($option);
                    } else {
                        continue; // Skip invalid option format
                    }
                    $selectedAttr = ($optionValue === $value) ? ' selected' : '';
                    $html .= '<option value="' . $optionValue . '"' . $selectedAttr . '>' . $optionLabel . '</option>';
                }
                $html .= '</select>';
                break;

            case 'radio':
                $html .= '<div class="options">';
                $optionIndex = 0;
                 foreach ($options as $option) {
                    $optionValue = '';
                    $optionLabel = '';
                    if (is_array($option) && isset($option['value'], $option['label'])) {
                        $optionValue = htmlspecialchars($option['value']);
                        $optionLabel = htmlspecialchars($option['label']);
                    } elseif (is_string($option)) {
                        $optionValue = htmlspecialchars($option);
                        $optionLabel = htmlspecialchars($option);
                    } else {
                        continue; // Skip invalid option format
                    }
                     $optionId = $fieldId . '_' . $optionIndex++;
                    $checkedAttr = ($optionValue === $value) ? ' checked' : '';
                    // Note: Radios in a group share the same NAME
                    $html .= '<input type="radio" name="' . $name . '" id="' . $optionId . '" value="' . $optionValue . '"' . $checkedAttr . $requiredAttr . '>';
                    $html .= '<label for="' . $optionId . '">' . $optionLabel . '</label>';
                 }
                 $html .= '</div>';
                // Radio buttons often have required applied to the group implicitly by checking one,
                // but HTML5 allows 'required' on each if you want browser validation to force a choice.
                break;

            case 'checkbox':
                $html .= '<div class="options">';
                 $optionIndex = 0;
                 // Checkboxes usually represent multiple selections, so 'value' isn't used for default state like radio/select.
                 // You'd typically handle default checked status differently if needed (e.g., passing an array of checked values).
                 // For simplicity here, we're not handling default checked state for checkboxes based on a single 'value'.
                foreach ($options as $option) {
                    $optionValue = '';
                    $optionLabel = '';
                    if (is_array($option) && isset($option['value'], $option['label'])) {
                        $optionValue = htmlspecialchars($option['value']);
                        $optionLabel = htmlspecialchars($option['label']);
                    } elseif (is_string($option)) {
                        $optionValue = htmlspecialchars($option);
                        $optionLabel = htmlspecialchars($option);
                    } else {
                         continue; // Skip invalid option format
                    }
                    $optionId = $fieldId . '_' . $optionIndex++;
                    // Note: Checkbox group shares the same NAME (often with [])
                    // The 'value' attribute here is what gets submitted if checked.
                    $html .= '<input type="checkbox" name="' . $name . '" id="' . $optionId . '" value="' . $optionValue . '">'; // No checked/required per item easily here
                    $html .= '<label for="' . $optionId . '">' . $optionLabel . '</label>';
                }
                 $html .= '</div>';
                 // Handling 'required' for checkboxes (meaning at least one must be checked) often requires JavaScript.
                break;

            case 'email':
            case 'number':
            case 'date':
            case 'password':
            case 'text': // Fallback for text and others like email, number, etc.
            default:
                $html .= '<input type="' . $type . '" name="' . $name . '" id="' . $fieldId . '" placeholder="' . $placeholder . '" value="' . $value . '"' . $requiredAttr . '>';
                break;
        }

        $html .= '</div>'; // Close form-field
    }
    return $html;
}

?>