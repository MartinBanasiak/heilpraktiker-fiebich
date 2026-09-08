<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/2/2015
 * Time: 1:35 PM
 */

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/2/2015
 * Time: 10:04 AM
 */
interface UnitOfWorkInterface
{

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function registerNew(Entity $entity);

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function registerClean(Entity $entity);

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function registerDirty(Entity $entity);

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function registerRemoved(Entity $entity);

    /**
     * @param Entity $entity
     * @param $state
     * @return mixed
     */
    public function registerEntity(Entity $entity, $state);

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function unregisterEntity(Entity $entity);

    public function clear();

    public function commit();

    public function rollback();
}