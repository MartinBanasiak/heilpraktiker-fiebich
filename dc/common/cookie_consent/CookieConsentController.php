<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.01.2018
 * Time: 15:18
 */

namespace DynCom\dc\common\cookie_consent;


class CookieConsentController
{
    /**
     * @var CookieConsentViewModel
     */
    protected $viewModel;
    /**
     * @var \Mustache_Engine
     */
    protected $mustacheEngine;

    public function __construct(CookieConsentViewModel $viewModel)
    {
        $this->init($viewModel);
    }

    protected function init(CookieConsentViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        $mustacheOptions = [
            'loader' => new \Mustache_Loader_FilesystemLoader(__DIR__),
        ];
        $this->mustacheEngine = new \Mustache_Engine($mustacheOptions);
    }

    public function handleRequest(): string
    {
        return $this->mustacheEngine->render('cookie_consent_view',$this->viewModel);
    }
}