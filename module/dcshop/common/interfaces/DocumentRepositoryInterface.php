<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 15.01.2015
 * Time: 18:32
 */
interface DocumentRepositoryInterface extends GenericRepositoryInterface {

    /**
     * Gets document-lines for passed document from DB, sets them in Document and adds or updates (by ==-comparison for complete object or db-id only)
     * the collection of the repository, so that future queries will return the updated object-instance with document-lines
     *
     * @param   GenericDocumentInterface $docInstance An instance of the specific document class
     *
     * @return  bool                                  Success or failure
     */
    public function setLinesForDocument( GenericDocumentInterface $docInstance );

}