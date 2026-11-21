<?php

return [
    'bkash_sandbox_enabled' => filter_var(getenv('BKASH_SANDBOX_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    'bkash_sandbox_base_url' => getenv('BKASH_SANDBOX_BASE_URL') ?: 'https://tokenized.sandbox.bka.sh/v1.2.0-beta',
    'bkash_sandbox_app_key' => getenv('BKASH_SANDBOX_APP_KEY') ?: '',
    'bkash_sandbox_app_secret' => getenv('BKASH_SANDBOX_APP_SECRET') ?: '',
    'bkash_sandbox_username' => getenv('BKASH_SANDBOX_USERNAME') ?: '',
    'bkash_sandbox_password' => getenv('BKASH_SANDBOX_PASSWORD') ?: '',
    'bkash_sandbox_callback_url' => getenv('BKASH_SANDBOX_CALLBACK_URL') ?: '',
    'bkash_demo_mode' => true,
];
