<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 11:13
 */
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\dcShop\classes\ShopLanguage;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:20
 */
interface DocumentLineRepository extends Repository {
    /**
     * @param GenericDocLineInterface $instance
     * @param DocumentRepositoryInterface $docRepository
     * @return mixed
     */
    public function getDocForDocLine( GenericDocLineInterface $instance, DocumentRepositoryInterface $docRepository );

    /**
     * @param GenericDocLineInterface $instance
     * @param ShopLanguage $shopLanguage
     * @return mixed
     */
    public function getItemForDocLine( GenericDocLineInterface $instance, ShopLanguage $shopLanguage );

}