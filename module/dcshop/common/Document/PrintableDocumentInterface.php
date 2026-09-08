<?php
/**
 * Created by PhpStorm.
 * User: alsotohy
 * Date: 26.02.2018
 * Time: 10:08
 */

namespace DynCom\dc\dcShop\Document;


interface PrintableDocumentInterface
{


    public function getDocumentNo();

    public function getDocumentName();

    public function getDocumentDate();

    public function getDocumentType();

    public function getDocumentTypeText();

    public function getRelatedDocuments();

    public function getDocumentAmount();

    public function getDocumentEmail();

    public function showPdfButton();

    public function getCustomerNo();


    public function hasRelatedDocuments();





}