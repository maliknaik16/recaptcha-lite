<?php

/**
 * @file
 * Contains RecaptchaLite\Components\V2Checkbox.
 */
namespace RecaptchaLite\Components;

defined('ABSPATH') || exit;

use RecaptchaLite\RecaptchaBase;

/**
 * The Google reCAPTCHA version 2 checkbox component. This class is responsible
 * for integrating the Google reCAPTCHA v2 Checkbox on the front end of the website.
 */
class V2Checkbox extends RecaptchaBase
{
    /**
     * {@inheritdoc}
     */
    public function enqueueStylesAndScripts($query_string = '')
    {
        // Generate the query string.
        $query_string = "?onload=grl_recaptcha_v2_render&render=explicit";

        // Enqueue the required styles and scripts.
        parent::enqueueStylesAndScripts($query_string);

        // Pass `grl_recaptcha` object to the script.
        wp_localize_script("grl_recaptcha_custom_script", "grl_recaptcha", [
            "site_key" => $this->site_key,
            'version' => $this->recaptcha_version,
            "theme" => $this->theme,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function renderCaptcha()
    {
        echo '<div class="grl-recaptcha"></div>';
    }
}

