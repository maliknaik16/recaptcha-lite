<?php

/**
 * @file
 * Contains GoogleRecaptchaLite\Components\V3.
 */
namespace GoogleRecaptchaLite\Components;

defined('ABSPATH') || exit;

use GoogleRecaptchaLite\GoogleRecaptchaBase;

/**
 * The Google reCAPTCHA version 3 component. This class is responsible for
 * integrating the Google reCAPTCHA v3 on the front end of the website.
 */
class V3 extends GoogleRecaptchaBase
{
    /**
     * Mapping of hook name to the action name.
     *
     * @var array
     */
    protected $hook_to_action_name = [
        'login_form' => 'login-form',
        'register_form' => 'register-form',
        'retrieve_password' => 'get-password',
        'lostpassword_form' => 'lost-password-form',
        'resetpass_form' => 'reset-password-form',
        'comment_form_after_fields' => 'comment-form',
        'woocommerce_register_form' => 'woo-register-form',
        'woocommerce_lostpassword_form' => 'woo-lost-password-form',
        'woocommerce_after_order_notes' => 'woo-order-checkout',
        'woocommerce_login_form' => 'woo-login-form',
        'bp_after_signup_profile_fields' => 'buddypress-signup',
    ];

    /**
     * Instantiate the component.
     */
    public function __construct()
    {
        parent::__construct();
        add_action('grl_recaptcha_before_verification', [$this, 'verifyActionName']);
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
     * Fires when the form is submitted.
     *
     * @return string
     */
    public function verifyActionName()
    {
        if (isset($_POST['g-recaptcha-action']) && $this->recaptcha_version === 'v3') {
            $action_name = $_POST['g-recaptcha-action'];
            $error_code = '';

            if (!$this->isActionNameValid($action_name)) {
                $error_code = 'malformed-action-name';
            }

            if (!empty($error_code)) {
                return $this->generateErrorMessage($error_code);
            }
        }

        return '';
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
        echo sprintf('<input type="hidden" value="%s" id="g-recaptcha-action" name="g-recaptcha-action">', $this->getActionNameFromHook(current_action()));
        echo '<input type="hidden" value="token" id="g-recaptcha-response" name="g-recaptcha-response"/>';
    }
}

