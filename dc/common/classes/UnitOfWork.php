<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\interfaces\RepositoryUnitOfWorkInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/2/2015
 * Time: 10:04 AM
 */
class UnitOfWork implements RepositoryUnitOfWorkInterface
{

    const ENTITY_STATE_NEW = 'ENTITY_STATE_NEW';
    const ENTITY_STATE_CLEAN = 'ENTITY_STATE_CLEAN';
    const ENTITY_STATE_DIRTY = 'ENTITY_STATE_DIRTY';
    const ENTITY_STATE_REMOVED = 'ENTITY_STATE_REMOVED';

    protected $possibleStates = array(self::ENTITY_STATE_NEW, self::ENTITY_STATE_CLEAN, self::ENTITY_STATE_DIRTY, self::ENTITY_STATE_REMOVED);
    protected $repositories;
    protected $entityCollection;
    protected $db;

    /**
     * UnitOfWork constructor.
     * @param GenericDBQueryWrapperInterface $queryWrapper
     */
    public function __construct(GenericDBQueryWrapperInterface $queryWrapper)
    {
        $this->entityCollection = new GenericObjectStorage();
        $this->db = $queryWrapper;
    }

    /**
     * @param Repository $repository
     */
    public function registerRepository(Repository $repository)
    {
        $objectClass = $repository->getObjectClass();
        $this->repositories[$objectClass] = $repository;
    }

    /**
     * @param GenericDBModelInterface $entity
     */
    public function registerNew(GenericDBModelInterface $entity)
    {
        $this->registerEntity($entity, self::ENTITY_STATE_NEW);
    }

    /**
     * @param GenericDBModelInterface $entity
     */
    public function registerClean(GenericDBModelInterface $entity)
    {
        $this->registerEntity($entity, self::ENTITY_STATE_CLEAN);
    }

    /**
     * @param GenericDBModelInterface $entity
     */
    public function registerDirty(GenericDBModelInterface $entity)
    {
        $this->registerEntity($entity, self::ENTITY_STATE_DIRTY);
    }

    /**
     * @param GenericDBModelInterface $entity
     */
    public function registerRemoved(GenericDBModelInterface $entity)
    {
        $this->registerEntity($entity, self::ENTITY_STATE_REMOVED);
    }

    /**
     * @param GenericDBModelInterface $entity
     * @param $state
     */
    public function registerEntity(GenericDBModelInterface $entity, $state)
    {
        if (!in_array($state, $this->possibleStates, true)) {
            throw new \InvalidArgumentException('State \'' . $state . '\' to register is not defined');
        }
        $className = get_class($entity);
        if (!array_key_exists($className, $this->repositories)) {
            throw new \InvalidArgumentException('UoW has no repository for entity of class ' . $className);
        }
        $data = array('className' => get_class($entity),'state' => $state);

        if (isset($entity->id) && ((int)$entity->id > 0)) {
            $this->repositories[$className]->lockRecordByID((int)$entity->id);
        }
        if($this->entityCollection->contains($entity)) {
            $data = $this->entityCollection->offsetGet($entity);
            $existingObjState = $data['state'];
            if($existingObjState === $state) {
                $this->entityCollection->detach($entity);
            } else {
                throw new \LogicException('Cannot register the same entity with different states');
            }
        }
        $this->entityCollection->attach($entity, $data);
    }

    /**
     * @param GenericDBModelInterface $entity
     */
    public function unregisterEntity(GenericDBModelInterface $entity)
    {
        $className = get_class($entity);
        if (!array_key_exists($className, $this->repositories)) {
            throw new \InvalidArgumentException('UoW has no repository of entity of class ' . $className);
        }
        if ($this->entityCollection->contains($entity)) {
            $this->entityCollection->detach($entity);
        }
        if (isset($entity->id) && ((int)$entity->id > 0)) {
            $this->repositories[$className]->releaseRecordLock((int)$entity->id);
        }
    }

    /**
     * @param Repository $repository
     */
    public function unregisterRepository(Repository $repository)
    {
        $className = $repository->getObjectClass();
        if (array_key_exists($className, $this->repositories)) {
            unset($this->repositories[$className]);
        }
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->entityCollection->clear();
        return $this;
    }

    public function commit()
    {

        if (!$this->db->startTransaction()) {
            throw new \BadMethodCallException('Could not start transaction. ' . $this->db->getErrorMessage());
        }

        $c = $this->entityCollection;
        $c->rewind();
        while ($c->valid()) {

            $object = $c->current();
            $data = $c->getInfo();
            $className = $data['className'];
            $state = $data['state'];
            $repository = $this->repositories[$className];
            if($repository instanceof Repository && $object instanceof GenericDBModelInterface) {
                switch ($state) {
                    case self::ENTITY_STATE_CLEAN:
                        break;
                    case self::ENTITY_STATE_NEW:
                        $currInsertQuery = $repository->getCreateInDBQuery($object);
                        $this->db->setQuery($currInsertQuery);
                        if (!$this->db->doQuery()) {
                            throw new \BadMethodCallException('Could not execute Query in transaction. ' . $this->db->getErrorMessage());
                        }
                        break;
                    case self::ENTITY_STATE_DIRTY:
                        $currUpdateQuery = $repository->getUpdateSingleQuery($object);
                        $this->db->setQuery($currUpdateQuery);
                        if (!$this->db->doQuery()) {
                            throw new \BadMethodCallException('Could not execute Query in transaction. ' . $this->db->getErrorMessage());
                        }
                        break;
                    case self::ENTITY_STATE_REMOVED:
                        $currDeleteQuery = $repository->getDeleteByIDQuery($object);
                        $this->db->setQuery($currDeleteQuery);
                        if (!$this->db->doQuery()) {
                            throw new \BadMethodCallException('Could not execute Query in transaction. ' . $this->db->getErrorMessage());
                        }
                        break;
                }
            }
            $c->next();
        }
        if (!$this->db->commitTransaction()) {
            throw new \BadMethodCallException('Could not commit transaction. ' . $this->db->getErrorMessage());
        }

        $c->rewind();
        while ($c->valid()) {

            $index = $c->key();
            $object = $c->current();
            $data = $c->getInfo();
            $className = $data['className'];
            $state = $data['state'];
            $repository = $this->repositories[$className];
            if ($repository instanceof Repository && $object instanceof GenericDBModelInterface)
            {
	    	switch ($state) {
                    case self::ENTITY_STATE_CLEAN: break;
                    case self::ENTITY_STATE_NEW:
                        $repository->add($object,false);
                        break;
                    case self::ENTITY_STATE_DIRTY:
                        $repository->releaseRecordLock($object->id);
                        $repository->recacheEntityFromDBByID($object->id);
                        break;
                    case self::ENTITY_STATE_REMOVED:
                        $repository->releaseRecordLock($object->id);
                        $repository->removeEntityFromCache($object);
                        break;
                }
	    }
            $c->next();
        }

        $this->clear();

    }

    public function rollback()
    {
        //Only release record locks - transaction actually happens only on commit(),
        //but entities with ID are locked from registration in their repositories
        $c = $this->entityCollection;
        $c->rewind();
        while ($c->valid()) {
            $index = $c->key();
            $object = $c->current();
            $data = $c->getInfo();
            $className = $data['className'];
            $state = $data['state'];
            $repository = $this->repositories[$className];
            if (isset($object->id) && ((int)$object->id > 0) && $repository instanceof Repository) {
                $repository->releaseRecordLock($object->id);
            }
            $c->next();
        }
        $c->rewind();
    }
}