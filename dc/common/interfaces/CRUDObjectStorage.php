<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 02:48
 */
interface CRUDObjectStorage
{

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function create(Entity $entity);

    /**
     * @param $id
     * @param $className
     * @param string $namespace
     * @return mixed
     */
    public function read($id, $className, $namespace = 'global');

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function update(Entity $entity);

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function replace(Entity $entity);

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function delete(Entity $entity);

    /**
     * @param $id
     * @param $className
     * @param string $namespace
     * @return mixed
     */
    public function getByID($id, $className, $namespace = 'global');

}