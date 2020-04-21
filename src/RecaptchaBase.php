<?php

/**
 * @file
 * Contains RecaptchaLite\RecaptchaBase.
 */
namespace RecaptchaLite;

defined('ABSPATH') || exit;

/**
 * The abstract RecaptchaBase class.
 */
abstract class RecaptchaBase
{
    /**
     * Hooks to attach the reCAPTCHA.
     *
     * @var array
     */
    protected $recaptcha_locations = [
        'login_form',
        'register_form',
        'lostpassword_form',
        'resetpass_form',
        'comment_form_after_fields',
        'woocommerce_register_form',
        'woocommerce_after_order_notes',
        'woocommerce_login_form',
        'bp_after_signup_profile_fields',
    ];

    /**
     * Instantiate the component.
     */
    public function __construct()
    {
        $this->site_key = get_option('grl_site_key');
        $this->recaptcha_version = get_option('grl_recaptcha_version');
        $this->theme = get_option('grl_theme');
    }

    /**
     * Enqueues the styles and scripts.
     *
     * @param string $query_string
     *
     * @return void
     */
    public function enqueueStylesAndScripts($query_string = '')
    {
        // Generate the extenal script url with query strings.
        $script_url = sprintf("https://www.google.com/recaptcha/api.js%s", $query_string);

        // Get the plugin directory location.
        $plugin_path = realpath(dirname(__FILE__));

        // Generate the local script url.
        $local_script_url = sprintf("%sassets/js/script.js", plugin_dir_url($plugin_path));

        // Generate the local style url.
        $local_style_url = sprintf("%sassets/css/style.css", plugin_dir_url($plugin_path));

        // Regsiter the Google reCAPTCHA api.js.
        wp_register_script('grl_recaptcha_api_script', $script_url);


        // Enqueue the scripts.
        wp_enqueue_script('grl_recaptcha_api_script');

        // Register the custom script.
        wp_register_script('grl_recaptcha_custom_script', $local_script_url, ['grl_recaptcha_api_script',], false, true);

        // Pass `grl_recaptcha` object to the script.
        wp_localize_script("grl_recaptcha_custom_script", "grl_recaptcha", [
            "site_key" => $this->site_key,
            'version' => $this->recaptcha_version,
        ]);

        // Enqueue the custom script.
        wp_enqueue_script('grl_recaptcha_custom_script');

        // Enqueue the custom style.
        wp_enqueue_style("grl_recaptcha_custom_style", $local_style_url);

        // Chop the 'ver' query string from the custom script url.
        add_filter('script_loader_src', [$this, 'removeVerQueryString'], 9999, 2);
    }

    /**
     * Chops the 'ver' query string from the script url only if the scripts is
     * related to the Google's `api.js`.
     *
     * @param string $src
     * @param string $handle
     *
     * @return string
     */
    public function removeVerQueryString( $src, $handle )
    {
        if ($handle === 'grl_recaptcha_api_script') {

            if (strpos($src, 'ver=')){
                $src = remove_query_arg('ver', $src);
            }
        }
        return $src;
    }

    /**
     * Renders the reCAPTCHA and hooks the verfication of the reCAPTCHA.
     *
     * @return void
     */
    public function loadCaptcha()
    {
        // Fire actions to render the captcha.
        foreach($this->recaptcha_locations as $hook) {

            if (substr($hook, 0, 11) === 'woocommerce') {
                $option_name = 'grl_woocommerce_forms';
            } else {
                $option_name = sprintf("grl_%s", $hook);
            }

            if (get_option($option_name) == '1') {
                add_action($hook, [$this, 'renderCaptcha']);
            }
        }

        // The hooks list that are fired when the form is in verification.
        $verification_hooks = [
            'preprocess_comment' => 'grl_comment_form_after_fields',
            'registration_errors' => 'grl_register_form',
            'lostpassword_post' => 'grl_lostpassword_form',
            'resetpass_post' => 'grl_resetpass_form',
            'woocommerce_register_post' => 'grl_woocommerce_forms',
            'woocommerce_checkout_process' => 'grl_woocommerce_forms',
            'wp_authenticate_user' => 'grl_login_form',
            'bp_signup_validate' => 'grl_bp_after_signup_profile_fields',
        ];

        // Fire actions to verfiy the captcha.
        foreach($verification_hooks as $hook => $option_name) {
            if (get_option($option_name) != '1') {
                continue;
            }

            if (substr($hook, 0, 11) === 'woocommerce') {
                if ($hook === 'woocommerce_checkout_process') {
                    add_action($hook, [$this, 'verifyOnCheckout']);
                } else {
                    add_action($hook, [$this, 'wooVerifyCaptcha'], 10, 3);
                }
            } elseif (substr($hook, 0, 3) === 'bp_') {
                add_action($hook, [$this, 'bpVerifyCaptcha']);
            }else {
                add_action($hook, [$this, 'verifyCaptcha']);
            }
        }
    }

    /**
     * Verifies the CAPTCHA results.
     *
     * @return void
     */
    public function verifyOnCheckout()
    {
        // Get the error message.
        $message = $this->getCaptchaVerificationMessage();

        if (!empty($message)) {
            wc_add_notice( $message, 'error' );
        }

    }

    /**
     * Verifies the CAPTCHA results.
     *
     * @return void
     */
    public function bpVerifyCaptcha()
    {
        //global $bp;

        // Get the error message.
        $message = $this->getCaptchaVerificationMessage();

        if (!empty($message)) {
            wp_die(new \WP_Error('grl_recaptcha', $message), 'reCAPTCHA Error', [
                'response' => 403,
                'back_link' => 1,
            ]);
            //$bp->signup->errors['grl-recaptcha-1'] = $message;
        }

    }

    /**
     * Verifies the CAPTCHA results and throws an error when something is wrong.
     *
     * @param string $username
     * @param string $email
     * @param mixed $errors
     *
     * @return void
     */
    public function wooVerifyCaptcha($username, $email, $errors)
    {
        // Get the error message.
        $message = $this->getCaptchaVerificationMessage();

        if (!empty($message)) {
            $errors->add('grl_recaptcha', $message);
        }

    }

    /**
     * Verifies the CAPTCHA results and throws an error when something is wrong.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function verifyCaptcha($input)
    {
        // Get the error message.
        $message = $this->getCaptchaVerificationMessage();

        if(!empty($message) && is_array($input)) {
            wp_die(new \WP_Error('grl_recaptcha', $message), 'reCAPTCHA Error', [
                'response' => 403,
                'back_link' => 1,
            ]);
        } elseif (!empty($message)) {
            return new \WP_Error('grl_recaptcha', $message);
        }

        return $input;
    }

    /**
     * Verifies the submitted form and returns a message. If the message is an
     * empty string then the verification is successfull, else there is some
     * error with the captcha verification.
     *
     * @return string
     */
    private function getCaptchaVerificationMessage()
    {
        // Get the request method for this request.
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($request_method == 'post' && isset($_POST['g-recaptcha-response'])) {
            // Get the reCAPTCHA response.
            $recaptcha_response = $_POST['g-recaptcha-response'];

            // Verify action name when the reCAPTCHA type is v3.
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

            // Get the secret key.
            $secret_key = get_option('grl_secret_key');

            // Generate the reCAPTCHA site verification url.
            $site_verify = 'https://www.google.com/recaptcha/api/siteverify';
            $site_verify .= sprintf("?secret=%s&response=%s", $secret_key, $recaptcha_response);

            // Get the verification results.
            $verify_response = wp_remote_get($site_verify);

            // Retrieve the body from the verification results.
            $response_body = wp_remote_retrieve_body($verify_response);

            // Decode the JSON response to an array.
            $response_body = json_decode($response_body, true);

            if ($response_body['success'] === false) {
                $error_code = 'invalid';

                // Get the error code from the result.
                if (array_key_exists('error-codes', $response_body) && count($response_body) > 0) {
                    $error_code = array_shift($response_body['error-codes']);
                }

                return $this->generateErrorMessage($error_code);
            }

        } else {
            return $this->generateErrorMessage('');
        }

        return '';
    }

    /**
     * Generates the HTml error message using the error code returned as response
     * from the `https://www.google.com/recaptcha/api/siteverify` endpoint.
     *
     * @param string $error_code
     *
     * @return string
     */
    public function generateErrorMessage($error_code)
    {
        // Get the error message using the error code.
        $error_message = $this->getErrorMessage($error_code);

        // Generate the message.
        $message = sprintf('<strong>ERROR: </strong> Google reCAPTCHA verification failed. %s', $error_message);

        return $message;
    }

    /**
     * Returns the error message.
     *
     * @param string $error_code
     *
     * @return string
     */
    public function getErrorMessage($error_code)
    {
        switch ($error_code) {
            case 'missing-input-secret':
                $error_message = 'The secret parameter is missing.';
                break;
            case 'missing-input-response':
                $error_message = 'The response parameter is missing.';
                break;
            case 'invalid-input-secret':
                $error_message = 'The secret parameter is invalid or malformed.';
                break;
            case 'invalid-input-response':
                $error_message = 'The response parameter is invalid or malformed.';
                break;
            case 'bad-request':
                $error_message = 'The request is invalid or malformed.';
                break;
            case 'timeout-or-duplicate':
                $error_message = 'The response is no longer valid: either is too old or has been used previously.';
                break;
            case 'malformed-action-name':
                $error_message = 'The action name has been malformed.';
                break;
            default:
                $error_message = 'Unknown error.';
        }

        return $error_message;
    }

    /**
     * Fire all the action or filters in this method.
     *
     * @return void
     */
    public function attach()
    {
        add_action('wp_loaded', [$this, 'loadCaptcha']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStylesAndScripts']);
        add_action('login_enqueue_scripts', [$this, 'enqueueStylesAndScripts']);
    }

    /**
     * Render the Google reCAPTCHA.
     *
     * @return void
     */
    abstract function renderCaptcha();

}