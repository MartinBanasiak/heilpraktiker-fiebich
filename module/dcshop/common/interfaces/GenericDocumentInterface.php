<?php
namespace DynCom\dc\dcShop\interfaces;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 14.01.2015
 * Time: 19:50
 */
use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\interfaces\GenericDBModelInterface;

/**
 * Class NavOrderDocument
 */
interface GenericDocumentInterface extends GenericDBModelInterface, \IteratorAggregate, \Countable {

    /**
     * @return bool
     */
    public function isLinesSet();

    /**
     * @return array
     */
    public function getLines();

    /**
     * @param \DynCom\dc\common\interfaces\GenericCollectionInterface $lines
     *
     * @return bool
     */
    public function setLines( GenericCollectionInterface $lines );

    /**
     * @param $propName
     *
     * @return bool
     */
    public function __isset( $propName );

    /**
     * @return  array   Array of criteria (fields compared to values) in disjunctive normal form for consumption by
     *                  line-repository to return the lines belonging to the document instance
     */
    public function getLineCriteriaArray();

}