<?php

/**
 * @file
 * Contains GoogleRecaptchaLite\GoogleRecaptchaLite.
 */
namespace GoogleRecaptchaLite;

defined('ABSPATH') || exit;

/**
 * This class is responsible for adding a new menu item in the dashboard with
 * the title 'Google reCAPTCHA' and also registers and renders the fields.
 */
class GoogleRecaptchaLite
{
    /**
     * The checkboxes for the reCAPTCHA locations.
     *
     * @var array
     */
    protected $checkboxes = [
        'login_form' => 'Login Form',
        'register_form' => 'Registration Form',
        'retrieve_password' => 'Retrieve Password',
        'lostpassword_form' => 'Lost Password Form',
        'resetpass_form' => 'Reset Password Form',
        'comment_form_after_fields' => 'Comment Form',
        'bp_after_signup_profile_fields' => 'Buddy Press Signup',

        // 'login_form' => 'Login Form',
        // 'registration_form' => 'Registration Form',
        // 'lost_password_form' => 'Lost password Form',
        // 'comment_form' => 'Comments Form',
        // 'reset_password_form' => 'Reset Password Form',
        // 'woocommerce_login_form' => 'WooCommerce Login Form',
    ];

    /**
     * Initialize the object.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'addOptionsPage']);
        add_action('admin_init', [$this, 'registerSettingsForm']);
        add_filter('plugin_action_links_google-recaptcha-lite/google-recaptcha-lite.php', [$this, 'addSettingsLink']);

        if (in_array('woocommerce/woocommerce.php', (array)get_option('active_plugins', []))) {
            $this->checkboxes = array_merge($this->checkboxes, [
                'woocommerce_register_form' => 'WooCommerce Registration Form',
                'woocommerce_lostpassword_form' => 'WooCommerce Lost Password Form',
                'woocommerce_after_order_notes' => 'WooCommerce Order Checkout Form',
                'woocommerce_login_form' => 'WooCommerce Login Form',
            ]);
        }
    }

    /**
     * Adds a settings link in the plugins page.
     *
     * @param array $links
     *
     * @return array
     */
    public function addSettingsLink($links)
    {
        // Generate the settings HTML link.
        $settings_link = sprintf('<a href="options-general.php?page=grl-options">%s</a>', __('Settings'));

        // Add the settings link to the links.
        array_push($links, $settings_link);

        return $links;
    }
    /**
     * Adds the Google reCAPTCHA menu item into the options page.
     *
     * @return void
     */
    public function addOptionsPage()
    {
        add_options_page(
            'Google reCAPTCHA Settings',
            'Google reCAPTCHA',
            'manage_options',
            'grl-options',
            [$this, 'renderOptionsPage']
        );
    }

    /**
     * Renders the Google reCAPTCHA settings.
     *
     * @return void
     */
    public function renderOptionsPage()
    {
        $output = '<div class="wrap">';
        $output .= '<form action="options.php" method="post">';
        $output .= '<h1>Google reCAPTCHA Settings</h1>';

        echo $output;

        settings_fields('grl-options');
        do_settings_sections('grl-options');
        submit_button();

        $output = '</form>';
        echo $output;
    }

    /**
     * Registers the form fields.
     *
     * @return void
     */
    public function registerSettingsForm()
    {
        // Add the settings section to the `grl-options` page.
        add_settings_section(
            'grl-recaptcha-settings',
            '',//__('Google reCAPTCHA API Keys', 'google-recaptcha-lite'),
            [$this, 'settingsDescription'],
            'grl-options'
        );

        // Add the reCAPTCHA version checkbox to the `grl-recaptcha-settings`
        // section.
        add_settings_field(
            'grl_recaptcha_version',
            'reCAPTCHA version',
            [$this, 'renderCaptchaVersion'],
            'grl-options',
            'grl-recaptcha-settings'
        );

        // Add the Site key text field to the `grl-recaptcha-settings` section.
        add_settings_field(
            'grl_site_key',
            'Site Key',
            [$this, 'renderSiteKeyElement'],
            'grl-options',
            'grl-recaptcha-settings'
        );

        // Add the Secret key text field to the `grl-recaptcha-settings` section.
        add_settings_field(
            'grl_secret_key',
            'Secret Key',
            [$this, 'renderSecretKeyElement'],
            'grl-options',
            'grl-recaptcha-settings'
        );


        // Add the Secret key text field to the `grl-recaptcha-settings` section.
        add_settings_field(
            'grl_recaptcha_locations',
            'Enable reCAPTCHA in',
            [$this, 'renderRecaptchaLocations'],
            'grl-options',
            'grl-recaptcha-settings'
        );

        // Add the Secret key text field to the `grl-recaptcha-settings` section.
        add_settings_field(
            'grl_theme',
            'Theme',
            [$this, 'renderThemeOption'],
            'grl-options',
            'grl-recaptcha-settings'
        );

        // Register the field options.
        register_setting('grl-options', 'grl_site_key');
        register_setting('grl-options', 'grl_secret_key');
        register_setting('grl-options', 'grl_recaptcha_version');
        register_setting('grl-options', 'grl_theme');

        foreach($this->checkboxes as $key => $value) {
            register_setting('grl-options', 'grl_' . $key);
        }

    }

    /**
     * Renders the Theme option checkbox.
     *
     * @return void
     */
    public function renderThemeOption()
    {
        // Option name for the theme preference.
        $option_name = 'grl_theme';

        $output = "<fieldset>";

        // Get attributes of the checkbox.
        $attributes = sprintf('id="%1$s" name="%1$s" value="1" %2$s', $option_name, checked(1, get_option($option_name), false));

        $output .= sprintf('<label for="%s">', $option_name);
        $output .= sprintf('<input type="checkbox" %s />', $attributes);
        $output .= 'Use Dark theme';
        $output .= '</label>';
        $output .= '<p class="description">This option only works with Google reCAPTCHA v2.</p>';

        $output .= "</fieldset>";

        echo $output;

    }

    /**
     * Renders the recaptcha_version text field.
     *
     * @return void
     */
    public function renderCaptchaVersion()
    {
        // Option name for the reCAPTCHA type.
        $option_name = 'grl_recaptcha_version';

        // Get the value of the reCAPTCHA version using the `Options API`.
        $recaptcha_version = get_option($option_name);

        $output = sprintf('<select id="%1$s" name="%1$s">', $option_name);
        // reCAPTCHA v2 checkbox option
        $output .= '<option value="v2-checkbox"';
        $output .= selected('v2-checkbox', $recaptcha_version, false) . '>';
        $output .= 'v2 Checkbox</option>';

        // reCAPTCHA v3 option
        $output .= '<option value="v3"';
        $output .= selected('v3', $recaptcha_version, false) . '>v3';
        $output .= '</option>';
        $output .= '</select>';

        echo $output;
    }

    /**
     * Renders the recaptcha_locations text field.
     *
     * @return void
     */
    public function renderRecaptchaLocations()
    {
        $output = "<fieldset>";
        foreach($this->checkboxes as $key => $value) {
            $option = 'grl_' . $key;

            // Get attributes for the checkbox.
            $attributes = sprintf('id="%1$s" name="%1$s" value="1" %2$s', $option, checked(1, get_option($option), false));

            $output .= sprintf('<label for="%s">', $option);
            $output .= sprintf('<input type="checkbox" %s />', $attributes);
            $output .= $value;
            $output .= '</label><br>';
        }

        $output .= "</fieldset>";

        echo $output;
    }

    /**
     * Renders the secret_key text field.
     *
     * @return void
     */
    public function renderSecretKeyElement()
    {
        // Option name for the secret key.
        $option_name = 'grl_secret_key';

        // Get the value of the reCAPTCHA secret key using the `Options API`.
        $secret_key = esc_attr(get_option($option_name));

        $output = '<input type="text" placeholder="Secret Key"';
        $output .= sprintf('class="regular-text" id="%1$s" name="%1$s"', $option_name);
        $output .= sprintf('value="%s" />', $secret_key);

        echo $output;
    }

    /**
     * Renders the site_key text field.
     *
     * @return void
     */
    public function renderSiteKeyElement()
    {
        // Option name for the site key.
        $option_name = 'grl_site_key';

        // Get the value of the reCAPTCHA site key using the `Options API`.
        $secret_key = esc_attr(get_option($option_name));

        $output = '<input type="text" placeholder="Site Key"';
        $output .= sprintf('class="regular-text" id="%1$s" name="%1$s"', $option_name);
        $output .= sprintf('value="%s" />', $secret_key);

        echo $output;
    }

    /**
     * Displays the section description.
     *
     * @return void
     */
    public function settingsDescription() {}
}
