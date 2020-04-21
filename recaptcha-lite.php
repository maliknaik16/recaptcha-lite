<?php

/**
 * Plugin Name: reCAPTCHA Lite
 * Plugin URI: http://wordpress.org/plugins/recaptcha-lite
 * Description: Integrate the Google's reCAPTCHA v2 Checkbox or v3 into the forms and protect your site from bots, brute-force attacks, spam, and abuse.
 * Version: 1.0
 * Author: Malik Naik
 * Author URI: http://maliknaik.me/
 * License: GNU General Public License v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: recaptcha-lite
 * Domain Path: /languages
 */

// Make sure we don't expose any info if called directly
defined('ABSPATH') || exit;

require_once 'autoload.php';

use RecaptchaLite\RecaptchaLite;
use RecaptchaLite\Factory\RecaptchaFactory;

new RecaptchaLite;
$component = RecaptchaFactory::create(get_option('grl_recaptcha_version', ''));
$component->attach();

