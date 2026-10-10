<?php
$gemini_api_key = trim(
    explode('=', file(dirname(__DIR__, 2) . '/.env')[0], 2)[1]
);

if (empty($gemini_api_key)) {
    throw new RuntimeException('Gemini API key is missing.');
}
?>