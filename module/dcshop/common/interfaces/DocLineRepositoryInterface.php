<?php
namespace DynCom\dc\dcShop\interfaces;
use DynCom\dc\dcShop\classes\ShopLanguage;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
interface DocLineRepositoryInterface extends GenericRepositoryInterface {
    /**
     * @param GenericDocLineInterface $instance
     *
     * @param DocumentRepositoryInterface $docRepository
     * @return GenericDocumentInterface
     */
    public function getDocForDocLine( GenericDocLineInterface $instance, DocumentRepositoryInterface $docRepository );

    /**
     * @param GenericDocLineInterface $instance
     * @param ShopLanguage $shopLanguage
     * @return WebshopItemInterface
     */
    public function getItemForDocLine( GenericDocLineInterface $instance, ShopLanguage $shopLanguage );

}