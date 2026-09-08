<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Michael Bauer
 * Date: 7/14/2015
 * Time: 2:19 AM
 */
interface PHTMLView extends View
{
    /**
     * PHTMLView constructor.
     * @param PHTMLTemplate $template
     * @param ViewModel $viewModel
     */
    public function __construct(PHTMLTemplate $template, ViewModel $viewModel);
}