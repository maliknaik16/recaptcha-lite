<?php

/**
 * Plugin Name: Google reCAPTCHA Lite
 * Plugin URI: http://wordpress.org/plugins/google-recaptcha-lite
 * Description: Integrates the Google reCAPTCHA v2 or v3 into the Comments Form, Login Form, Registration Form, etc.
 * Version: 1.0
 * Author: Malik Naik
 * Author URI: http://maliknaik.me/
 * License: GPL v3 or later
 * Text Domain: google-recaptcha-lite
 * Domain Path: /languages
 */

// require_once 'GoogleRecaptchaLite.php';
// require_once 'GoogleRecaptchaBase.php';
// require_once 'GoogleRecaptchaCheckbox.php';
// Make sure we don't expose any info if called directly

defined('ABSPATH') || exit;

require_once 'autoload.php';

use GoogleRecaptchaLite\GoogleRecaptchaLite;
use GoogleRecaptchaLite\Factory\GoogleRecaptchaFactory;

new GoogleRecaptchaLite;
$component = GoogleRecaptchaFactory::create(get_option('grl_recaptcha_version'));
$component->attach();

