<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.06.2017
 * Time: 11:15
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\common\interfaces\GenericCollectionInterface;
use DynCom\dc\common\traits\genericCollectionTrait;
use DynCom\dc\dcShop\interfaces\OrderableEntityInterface;
use Traversable;

class OrderableEntityCollection implements \IteratorAggregate, \Countable
{
    protected $orderableEntities;

    public function __construct()
    {
        $this->orderableEntities = new \SplObjectStorage();
    }


    public function addOrderableEntity(OrderableEntityInterface $entity)
    {
        $keys = [
            'id' => $entity->getID(),
            'identifier' => $entity->getIdentifier(),
            'sub_identifier' => $entity->getSubIdentifier(),
        ];
        if ($this->orderableEntities->contains($entity)) {
            return;
        }
        $this->orderableEntities->attach($entity,$keys);
    }

    public function remove(OrderableEntityInterface $entity)
    {
        $this->orderableEntities->detach($entity);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \IteratorIterator($this->orderableEntities);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->orderableEntities);
    }


}