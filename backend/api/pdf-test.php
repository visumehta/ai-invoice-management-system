<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "../config/config.php";

if (empty($gemini_api_key)) {
    echo json_encode([
        "success" => false,
        "message" => "Gemini API key is not configured"
    ]);
    exit;
}

include "../config/database.php";

// ===============================
// 1. Show Upload Form
// ===============================

// if ($_SERVER["REQUEST_METHOD"] == "GET") {
?>

<?php

// ===============================
// 2. Check PDF
// ===============================


if (!isset($_FILES["invoice_pdf"])) {

    // echo "Please select a PDF";
    echo json_encode([
        "success" => false,
        "error" => "Please select a PDF"
    ]);

    exit;
}


$file = $_FILES["invoice_pdf"];

if ($file["error"] != 0) {

    // echo "File upload failed";
    echo json_encode([
        "success" => false,
        "error" => "File upload failed"
    ]);

    exit;
}


$file_path = $file["tmp_name"];

// ===============================
// 3. Read PDF
// ===============================

$pdf_data = file_get_contents($file_path);

$pdf_base64 = base64_encode($pdf_data);

// ===============================
// 4. Gemini API
// ===============================


$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent";


// $prompt = "
// Read this invoice PDF carefully.

// Tell me:

// 1. Which marketplace is this invoice from?
// 2. What is the invoice number?
// 3. What is the order ID/order number?
// 4. What is the product name?
// 5. What is the SKU?
// 6. What is the quantity?
// 7. What is the total amount?

// Do not guess any information.
// If a field is not available, say null.
// ";

$prompt = "
Read this invoice PDF carefully and extract all available invoice information.

Important rules:
- Do not guess any value.
- If a value is not present in the invoice, return null.
- Extract the exact value as printed on the invoice.
- There is one product line in this invoice, but quantity may be more than 1.
- Return data according to the provided JSON schema.

Marketplace identification rules:
- marketplace: Return the original marketplace name or domain as printed in the invoice.
- normalized_marketplace: Identify the standard marketplace brand using evidence from the invoice, such as its name, domain, logo, or invoice wording.
- Use a short, consistent brand name for normalized_marketplace.
- Different domains or display names may belong to the same marketplace.
- Do not invent a marketplace or guess when evidence is insufficient.
- If the marketplace cannot be identified confidently, return null for normalized_marketplace.
";

// $data = [

//     "contents" => [

//         [

//             "parts" => [

//                 [
//                     "inline_data" => [
//                         "mime_type" => "application/pdf",
//                         "data" => $pdf_base64
//                     ]
//                 ],

//                 [
//                     "text" => $prompt
//                 ]

//             ]

//         ]

//     ]

// ];

$data = [

    "contents" => [

        [

            "parts" => [

                [
                    "inline_data" => [
                        "mime_type" => "application/pdf",
                        "data" => $pdf_base64
                    ]
                ],

                [
                    "text" => $prompt
                ]

            ]

        ]

    ],

    "generationConfig" => [

        "responseMimeType" => "application/json",

        "responseSchema" => [

            "type" => "OBJECT",

            "properties" => [

                "marketplace" => [
                    "type" => "STRING"
                ],

                "normalized_marketplace" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "order_id" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "invoice_no" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "order_date" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "invoice_date" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "seller_name" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "seller_gstin" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "seller_pan" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "customer_name" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "customer_address" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "customer_state" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "customer_pincode" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "sku" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "product_name" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "hsn" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "quantity" => [
                    "type" => "INTEGER",
                    "nullable" => true
                ],

                "gross_amount" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "discount" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "taxable_value" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "gst_rate" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "gst_type" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "gst_amount" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "cess" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "other_charges" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "total_amount" => [
                    "type" => "NUMBER",
                    "nullable" => true
                ],

                "payment_mode" => [
                    "type" => "STRING",
                    "nullable" => true
                ],

                "payment_transaction_id" => [
                    "type" => "STRING",
                    "nullable" => true
                ]

            ],

            "required" => [

                "marketplace",
                "normalized_marketplace",
                "order_id",
                "invoice_no",
                "order_date",
                "invoice_date",
                "seller_name",
                "seller_gstin",
                "seller_pan",
                "customer_name",
                "customer_address",
                "customer_state",
                "customer_pincode",
                "sku",
                "product_name",
                "hsn",
                "quantity",
                "gross_amount",
                "discount",
                "taxable_value",
                "gst_rate",
                "gst_type",
                "gst_amount",
                "cess",
                "other_charges",
                "total_amount",
                "payment_mode",
                "payment_transaction_id"

            ]

        ]

    ]

];

// ===============================
// 5. Send Request
// ===============================

$maxRetries = 3;

$response = false;
$http_code = 0;
$last_error = "";

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt(
        $ch,
        CURLOPT_CAINFO,
        "C:\wamp64\bin\apache\apache2.4.46\bin\cacert.pem"
    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        "Content-Type: application/json",

        "x-goog-api-key: " . $gemini_api_key

    ]);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($data)
    );


    $response = curl_exec($ch);

    if ($response === false) {
        $last_error = curl_error($ch);

        echo json_encode([
            "success" => false,
            "error" => curl_error($ch)
        ]);

        curl_close($ch);

        if ($attempt < $maxRetries) {
            sleep(2); // Wait for 2 seconds before retrying
            continue; // Retry the request
        }

        exit;
        // break; // Exit the loop after max retries
    }


    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Success
    if ($http_code == 200) {
        break;
    }
    // Retry for temporary Gemini errors
    if (
        $http_code == 500 ||
        $http_code == 502 ||
        $http_code == 503 ||
        $http_code == 504
    ) {

        if ($attempt < $maxRetries) {

            sleep(2);

            continue;
        }
    }

    // 429 = Quota exceeded
    if ($http_code == 429) {

        echo json_encode([
            "success" => false,
            "message" => "Gemini API quota exceeded. Please try again later.",
            "http_code" => 429,
            "error" => json_decode($response, true)
        ]);

        exit;
    }


    // Other error - don't retry
    break;
}

// ===============================
// 6. Check Gemini Response
// ===============================

// if ($http_code != 200) {
if ($response === false) {
    echo json_encode([
        "success" => false,
        "message" => "Gemini is temporarily unavailable. Please try again.",
        "http_code" => $http_code,
        "error" => json_decode($response, true)
    ]);


    exit;
}

if ($http_code != 200) {

    $gemini_response = json_decode(
        $response,
        true
    );

    echo json_encode([
        "success" => false,
        "message" => "Gemini is temporarily unavailable. Please try again.",
        "http_code" => $http_code,
        "error" => json_decode($response, true)
    ]);


    exit;
}

// ===============================
// 7. Get AI JSON Text
// ===============================

$gemini_response = json_decode(
    $response,
    true
);

$ai_text =
    $gemini_response["candidates"][0]["content"]["parts"][0]["text"];


$invoice = json_decode(
    $ai_text,
    true
);

if (!$invoice) {
    echo "AI JSON parsing failed:\n\n";

    echo $ai_text;
}


// ===============================
// 8. Prepare Values
// ===============================

function numberValue($value)
{
    if ($value === null || $value === "") {
        return null;
    }

    if (is_numeric($value)) {
        return $value;
    }

    return null;
}

$marketplace = $invoice["marketplace"] ?? null;

$normalized_marketplace = $invoice["normalized_marketplace"] ?? null;

$order_id = $invoice["order_id"] ?? null;

$invoice_no = $invoice["invoice_no"] ?? null;

$order_date = $invoice["order_date"] ?? null;

$invoice_date = $invoice["invoice_date"] ?? null;

$seller_name = $invoice["seller_name"] ?? null;

$seller_gstin = $invoice["seller_gstin"] ?? null;

$seller_pan = $invoice["seller_pan"] ?? null;

$customer_name = $invoice["customer_name"] ?? null;

$customer_address = $invoice["customer_address"] ?? null;

$customer_state = $invoice["customer_state"] ?? null;

$customer_pincode = $invoice["customer_pincode"] ?? null;

$sku = $invoice["sku"] ?? null;

$product_name = $invoice["product_name"] ?? null;

$hsn = $invoice["hsn"] ?? null;

$quantity = numberValue($invoice["quantity"] ?? null);

$gross_amount = numberValue($invoice["gross_amount"] ?? null);

$discount = numberValue($invoice["discount"] ?? null);

$taxable_value = numberValue($invoice["taxable_value"] ?? null);

$gst_rate = numberValue($invoice["gst_rate"] ?? null);

$gst_type = numberValue($invoice["gst_type"] ?? null);

$gst_amount = numberValue($invoice["gst_amount"] ?? null);

$cess = numberValue($invoice["cess"] ?? null);

$other_charges = numberValue($invoice["other_charges"] ?? null);

$total_amount = numberValue($invoice["total_amount"] ?? null);

$payment_mode = $invoice["payment_mode"] ?? null;

$payment_transaction_id =
    $invoice["payment_transaction_id"] ?? null;

// ===============================
// 9. PDF Path
// ===============================

// For now we are NOT permanently storing the PDF.

$original_pdf_path = null;

// ===============================
// 10. Save Raw AI Response
// ===============================

$ai_raw_response = $ai_text;

$order_date = !empty($order_date)
    ? date("Y-m-d", strtotime(str_replace(".", "-", $order_date)))
    : null;

$invoice_date = !empty($invoice_date)
    ? date("Y-m-d", strtotime(str_replace(".", "-", $invoice_date)))
    : null;

// ===============================
// 11. Prepare SQL Values
// ===============================

function sqlValue($value)
{
    global $conn;

    if ($value === null || $value === "") {
        return "NULL";
    }

    return "'" . mysqli_real_escape_string($conn, $value) . "'";
}

// Check duplicate invoice
$checkSql = "SELECT id FROM invoices
             WHERE marketplace = " . sqlValue($marketplace) . "
             AND order_id = " . sqlValue($order_id) . "
             AND invoice_no = " . sqlValue($invoice_no) . "
             LIMIT 1";

$checkResult = mysqli_query($conn, $checkSql);

if ($checkResult && mysqli_num_rows($checkResult) > 0) {

    echo json_encode([
        "success" => false,
        "message" => "This invoice already exists."
    ]);

    exit;
}


// ===============================
// 11. Insert into MySQL
// ===============================

$sql = "INSERT INTO invoices (

    marketplace,
    normalized_marketplace,
    order_id,
    invoice_no,
    order_date,
    invoice_date,

    seller_name,
    seller_gstin,
    seller_pan,

    customer_name,
    customer_address,
    customer_state,
    customer_pincode,

    sku,
    product_name,
    hsn,
    quantity,

    gross_amount,
    discount,
    taxable_value,

    gst_rate,
    gst_type,
    gst_amount,
    cess,

    other_charges,
    total_amount,

    payment_mode,
    payment_transaction_id,

    original_pdf_path,
    ai_raw_response

) VALUES (

    " . sqlValue($marketplace) . ",
    " . sqlValue($normalized_marketplace) . ",
    " . sqlValue($order_id) . ",
    " . sqlValue($invoice_no) . ",
    " . sqlValue($order_date) . ",
    " . sqlValue($invoice_date) . ",

    " . sqlValue($seller_name) . ",
    " . sqlValue($seller_gstin) . ",
    " . sqlValue($seller_pan) . ",

    " . sqlValue($customer_name) . ",
    " . sqlValue($customer_address) . ",
    " . sqlValue($customer_state) . ",
    " . sqlValue($customer_pincode) . ",

    " . sqlValue($sku) . ",
    " . sqlValue($product_name) . ",
    " . sqlValue($hsn) . ",
    " . sqlValue($quantity) . ",

    " . sqlValue($gross_amount) . ",
    " . sqlValue($discount) . ",
    " . sqlValue($taxable_value) . " ,

    " . sqlValue($gst_rate) . ",
    " . sqlValue($gst_type) . ",
    " . sqlValue($gst_amount) . ",
    " . sqlValue($cess) . ",

    " . sqlValue($other_charges) . ",
    " . sqlValue($total_amount) . ",

    " . sqlValue($payment_mode) . ",
    " . sqlValue($payment_transaction_id) . ",

    " . sqlValue($original_pdf_path) . ",
    " . sqlValue($ai_raw_response) . "

)";

$result = mysqli_query(
    $conn,
    $sql
);


// ===============================
// 12. Final Response
// ===============================

if ($result) {
    echo json_encode([
        "success" => true,
        "message" => "Invoice saved successfully",
        "invoice" => $invoice
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Database insert failed: " . mysqli_error($conn)
    ]);
}

?>