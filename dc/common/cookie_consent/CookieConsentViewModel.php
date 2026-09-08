<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.01.2018
 * Time: 14:28
 */

namespace DynCom\dc\common\cookie_consent;


use DynCom\dc\regionalization\RegionalizedTextProvider;

class CookieConsentViewModel
{
    /**
     * @var bool $cookieConsentRequired
     */
    public $cookieConsentRequired;
    /**
     * @var bool $alreadyConsented
     */
    public $alreadyConsented;
    /**
     * @var string $message
     */
    public $message;
    /**
     * @var string $buttonText
     */
    public $buttonText;

    /**
     * @var string $cookieName
     */
    public $cookieName;

    /**
     * @var int $cookieConsentExpirationDays
     */
    public $cookieConsentExpirationDays;

    /**
     * @var string $useSecureCookies
     */
    public $useSecureCookies;


    public function __construct(RegionalizedTextProvider $regionalizedTextProvider)
    {
        $this->setValues($regionalizedTextProvider);
    }

    protected function setValues(RegionalizedTextProvider $textProvider) : void
    {
        $consentRequiredEnvVal = getenv('COOKIE_CONSENT_REQUIRED');
        $this->cookieConsentRequired = (bool)($consentRequiredEnvVal ?: false);

        $cookieNameEnvVal = getenv('COOKIE_CONSENT_COOKIE_NAME');
        $this->cookieName = $cookieNameEnvVal ?: 'cookie_consent';

        $this->alreadyConsented = array_key_exists($this->cookieName,$_COOKIE) && (bool)$_COOKIE[$this->cookieName];

        $expirationDaysEnvVal = getenv('COOKIE_CONSENT_EXPIRATION_DAYS');
        $this->cookieConsentExpirationDays = (int)($expirationDaysEnvVal ?: 356);

        $useSecureCookiesEnvVal = getenv('USE_SECURE_COOKIES');
        $this->useSecureCookies = ($useSecureCookiesEnvVal ? 'true': 'false');

        $this->message = $textProvider->getRegionalizedText('cookie_consent_message');
        $this->buttonText = $textProvider->getRegionalizedText('cookie_consent_button_text');

    }
}