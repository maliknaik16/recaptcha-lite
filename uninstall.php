<?php

// Block direct access.
if (!defined('WP_UNINSTALL_PLUGIN') || !WP_UNINSTALL_PLUGIN) {
    echo 'Direct access not allowed';
    status_header(404);
    exit;
}

// Google reCAPTCHA Lite plugin options list.
$plugin_options = [
    'site_key',
    'secret_key',
    'theme',
    'recaptcha_version',
    'login_form',
    'register_form',
    'lostpassword_form',
    'resetpass_form',
    'comment_form_after_fields',
    'bp_after_signup_profile_fields',
    'woocommerce_forms',
];

// Delete all the saved options.
foreach ($plugin_options as $option_name) {
    delete_option(sprintf("grl_%s", $option_name));
}
