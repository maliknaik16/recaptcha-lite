<?php

/**
 * @file
 * Contains RecaptchaLite\Components\V3.
 */
namespace RecaptchaLite\Components;

defined('ABSPATH') || exit;

use RecaptchaLite\RecaptchaBase;

/**
 * The Google reCAPTCHA version 3 component. This class is responsible for
 * integrating the Google reCAPTCHA v3 on the front end of the website.
 */
class V3 extends RecaptchaBase
{
    /**
     * Mapping of hook name to the action name.
     *
     * @var array
     */
    protected $hook_to_action_name = [
        'login_form' => 'login_form',
        'register_form' => 'register_form',
        'lostpassword_form' => 'lost_password_form',
        'resetpass_form' => 'reset_password_form',
        'comment_form_after_fields' => 'comment_form',
        'woocommerce_register_form' => 'woo_register_form',
        'woocommerce_after_order_notes' => 'woo_order_checkout',
        'woocommerce_login_form' => 'woo_login_form',
        'bp_after_signup_profile_fields' => 'buddypress_signup',
    ];

    /**
     * Instantiate the component.
     */
    public function __construct()
    {
        add_action('wp_loaded', function () {
            add_action('grl_recaptcha_before_verification', [$this, 'verifyActionName']);
        });
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function enqueueStylesAndScripts($query_string = '')
    {
        // Generate the query string.
        $query_string = sprintf("?render=%s", $this->site_key);

        // Enqueue main styles and scripts of the Google reCAPTCHA.
        parent::enqueueStylesAndScripts($query_string);
    }

    /**
     * Returns the action name for the Google reCAPTCHA v3 using the hook name.
     *
     * @param string $hook_name
     *
     * @return string
     */
    public function getActionNameFromHook($hook_name)
    {
        if (array_key_exists($hook_name, $this->hook_to_action_name)){
            return $this->hook_to_action_name[$hook_name];
        }

        return '';
    }

    /**
     * Returns a boolean on whether the action name is valid or not.
     *
     * @param string $action_name
     *
     * @return bool
     */
    public function isActionNameValid($action_name)
    {
        if (array_search($action_name, $this->hook_to_action_name) !== false) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function renderCaptcha()
    {
        echo sprintf('<input type="hidden" value="%s" class="g-recaptcha-action" name="g-recaptcha-action">', $this->getActionNameFromHook(current_action()));
        echo '<input type="hidden" value="token" class="g-recaptcha-response" name="g-recaptcha-response"/>';
    }
}

