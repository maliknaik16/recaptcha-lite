<?php

/**
 * @file
 * Contains GoogleRecaptchaLite\Factory\GoogleRecaptchaFactory.
 */
namespace GoogleRecaptchaLite\Factory;

defined('ABSPATH') || exit;

use GoogleRecaptchaLite\Components\V2Checkbox;
use GoogleRecaptchaLite\Components\V3;

/**
 * This class is responsible for creating the reCAPTCHA component.
 */
class GoogleRecaptchaFactory
{
    /**
     * Mapping from reCAPTCHA type to Class name.
     *
     * @var array
     */
    protected static $map = [
        'v2-checkbox' => V2Checkbox::class,
        'v3' => V3::class,
    ];

    protected function __construct() {}

    /**
     * Instantiates and return the reCAPTCHA component.
     *
     * @param string $type
     *
     * @return GoogleRecaptchaLite\GoogleRecaptchaBase
     */
    public static function create($type = '')
    {
        if (array_key_exists($type, self::$map)) {
            return new self::$map[$type];
        }

        return new self::$map['v2-checkbox'];
    }
}