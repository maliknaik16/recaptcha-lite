=== reCAPTCHA Lite ===
Contributors: maliknaik16
Tags: google, bots, protect, secure, captcha, v3, woocommerce, v2, form, login, register, reset, lost, buddypress, lite, block, no-interaction
Requires at least: 4.4
Tested up to: 5.4
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Integrate the Google's reCAPTCHA Google's reCAPTCHA v2 Checkbox or v3 into the forms and protect your site from bots, brute-force attacks, spam, and abuse.

== Description ==
The reCAPTCHA Lite protects your WordPress site from the bots, brute-force attacks, spam, and abuse. This plugin comes with the support of reCAPTCHA v3 and v2 Checkbox. The reCAPTCHA v3 allows users to navigate site without solving "I'm not a robot" challenges.

You can integrate the Google's reCAPTCHA in the following forms:

* Login Form
* Registration Form
* Lost Password Form
* Reset Password Form
* Comment Form
* WooCommerce Forms
* Buddy Press Signup Form

If you are using the Google reCAPTCHA v3 then the following actions list shows the action names that will be used to hit the [Google's reCAPTCHA API endpoint](https://developers.google.com/recaptcha/docs/verify#api_request) during validation. For more information on actions checkout out the [official documentation](https://developers.google.com/recaptcha/docs/v3#actions).

* login_form
* register_form
* lost_password_form
* reset_password_form
* comment_form
* woo_register_form
* woo_order_checkout
* woo_login_form
* buddypress_signup

== Installation ==
Download and extract this plugin to the `/wp-content/plugins/` folder and activate the plugin from Plugins menu in WordPress Admin Dashboard.

== Frequently Asked Questions ==
= What is Google reCAPTCHA v3? =
The reCAPTCHA v3 is a new version of the Google's reCAPTCHA that allows users to navigate the sites without having to solve "I'm not a robot" challenge.
= I'm getting "ERROR for site owner" error, what should I do? =
Make sure that you are using the right site key and secret key of the reCAPTCHA type. For example, if you use site key, and secret key of the reCAPTCHA v2 then these keys won't work for reCAPTCHA v3.

== Screenshots ==
1. reCAPTCHA Lite - Settings
2. reCAPTCHA Lite - Login form with reCAPTCHA v3
3. reCAPTCHA Lite - Action name malformed error
4. reCAPTCHA Lite - Buddy press signup form
5. reCAPTCHA Lite - WooCommerce Login and Register Forms with Dark theme
6. reCAPTCHA Lite - WooCommerce Checkout Form
7. reCAPTCHA Lite - WooCommerce Captcha error on checkout

== Changelog ==
= 1.0 =
* First Release