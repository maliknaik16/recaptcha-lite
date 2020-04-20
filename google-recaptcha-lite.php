<?php

/**
 * Plugin Name: Google reCAPTCHA Lite
 * Plugin URI: http://wordpress.org/plugins/google-recaptcha-lite
 * Description: Integrates the Google reCAPTCHA v2 or v3 into the Comments Form, Login Form, Registration Form, etc.
 * Version: 1.0
 * Author: Malik Naik
 * Author URI: http://maliknaik.me/
 * License: GNU General Public License v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: google-recaptcha-lite
 * Domain Path: /languages
 */

// Make sure we don't expose any info if called directly
defined('ABSPATH') || exit;

require_once 'autoload.php';

use GoogleRecaptchaLite\GoogleRecaptchaLite;
use GoogleRecaptchaLite\Factory\GoogleRecaptchaFactory;

new GoogleRecaptchaLite;
$component = GoogleRecaptchaFactory::create(get_option('grl_recaptcha_version'));
$component->attach();

