<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 14:18
 */
interface RepositoryUnitOfWorkInterface
{

    /**
     * @param Repository $repository
     */
    public function registerRepository(Repository $repository);

    /**
     * @param Repository $repository
     */
    public function unregisterRepository(Repository $repository);

    /**
     * @param GenericDBModelInterface $entity
     * @return mixed
     */
    public function registerNew(GenericDBModelInterface $entity);

    /**
     * @param GenericDBModelInterface $entity
     * @return mixed
     */
    public function registerClean(GenericDBModelInterface $entity);

    /**
     * @param GenericDBModelInterface $entity
     * @return mixed
     */
    public function registerDirty(GenericDBModelInterface $entity);

    /**
     * @param GenericDBModelInterface $entity
     * @return mixed
     */
    public function registerRemoved(GenericDBModelInterface $entity);

    /**
     * @param GenericDBModelInterface $entity
     * @param $state
     * @return mixed
     */
    public function registerEntity(GenericDBModelInterface $entity, $state);

    /**
     * @param GenericDBModelInterface $entity
     * @return mixed
     */
    public function unregisterEntity(GenericDBModelInterface $entity);

    public function clear();

    public function commit();

    public function rollback();

}