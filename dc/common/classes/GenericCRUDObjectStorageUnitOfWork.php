<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CRUDObjectStorage;
use DynCom\dc\common\interfaces\Entity;
use DynCom\dc\common\interfaces\Observer;
use DynCom\dc\common\interfaces\UnitOfWorkInterface;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 13:59
 */
class GenericCRUDObjectStorageUnitOfWork implements UnitOfWorkInterface,Observer
{

    const ENTITY_STATE_NEW = 'ENTITY_STATE_NEW';
    const ENTITY_STATE_CLEAN = 'ENTITY_STATE_CLEAN';
    const ENTITY_STATE_DIRTY = 'ENTITY_STATE_DIRTY';
    const ENTITY_STATE_REMOVED = 'ENTITY_STATE_REMOVED';

    const EVENT_SUBSTR_MODEL_CHANGED = '.changed';

    protected $storage;
    protected $newEntities;
    protected $cleanEntities;
    protected $cleanEntitiesInit;
    protected $dirtyEntities;
    protected $removedEntities;

    /**
     * GenericCRUDObjectStorageUnitOfWork constructor.
     * @param CRUDObjectStorage $storage
     */
    public function __construct(CRUDObjectStorage $storage) {
        $this->storage = $storage;
    }

    /**
     * @param $eventName
     * @param $data
     */
    public function notify($eventName, $data) {
        $className = get_class($data);
        if(!$className || !($data instanceof Entity)) return;
        $id = $data->getID();
        $this->checkEventForChangedClean($eventName,$className,$id);
    }

    /**
     * @param $eventName
     * @param $className
     * @param $id
     */
    protected function checkEventForChangedClean($eventName, $className, $id) {
        if(array_key_exists($className,$this->cleanEntities) && array_key_exists($id,$this->cleanEntities[$className]) && false !== stripos($eventName,self::EVENT_SUBSTR_MODEL_CHANGED)) {
            if(isset($this->cleanEntities[$className][$id])) {
                $entity = $this->cleanEntities[$className][$id];
                $this->dirtyEntities[$className][$id] = $entity;
                unset($this->cleanEntities[$className][$id]);
            }
        }
    }

    /**
     * @param Entity $entity
     */
    public function registerNew(Entity $entity) {
        $className = get_class($entity);
        $id = $entity->getID();
        if((int)$id > 0) {
            throw new \BadMethodCallException(
              "Entity must not have an ID > 0 to be registered as new."
            );
        }
        $objHash = spl_object_hash($entity);
        $this->newEntities[$className][$objHash] = &$entity;
    }

    /**
     * @param Entity $entity
     */
    public function registerClean(Entity $entity) {
        $className = get_class($entity);
        $id = $entity->getID();
        if(!(int)$id > 0) {
            throw new \BadMethodCallException(
              "Entity needs a not-null id to be registered as clean."
            );
        }
        if(
                (array_key_exists($className,$this->dirtyEntities) && array_key_exists($id,$this->dirtyEntities[$className]))
            ||  (array_key_exists($className,$this->removedEntities) && array_key_exists($id,$this->removedEntities[$className]))) {

            throw new \BadMethodCallException(
              "Entity must not already be registered as dirty or removed to be registered as Clean"
            );
        }
        $this->cleanEntitiesInit[$className][$id] = clone $entity;
        $this->cleanEntities[$className][$id] = &$entity;
    }

    /**
     * @param Entity $entity
     */
    public function registerDirty(Entity $entity) {
        $className = get_class($entity);
        $id = $entity->getID();
        if(!(int)$id > 0) {
            throw new \BadMethodCallException(
                "Entity needs a not-null id to be registered as dirty."
            );
        }
        unset($this->cleanEntities[$className][$id],$this->removedEntities[$className][$id]);
        $this->dirtyEntities[$className][$id] = &$entity;
    }

    /**
     * @param Entity $entity
     */
    public function registerRemoved(Entity $entity) {
        $className = get_class($entity);
        $id = $entity->getID();
        if(!(int)$id > 0) {
            throw new \BadMethodCallException(
                "Entity needs a not-null id to be registered as removed."
            );
        }
        unset($this->cleanEntities[$className][$id],$this->dirtyEntities[$className][$id]);
        $this->removedEntities[$className][$id] = &$entity;
    }

    /**
     * @param Entity $entity
     * @param $state
     */
    public function registerEntity(Entity $entity, $state) {
        switch($state) {
            case self::ENTITY_STATE_NEW:
                $this->registerNew($entity);
                return;
            case self::ENTITY_STATE_CLEAN:
                $this->registerClean($entity);
                return;
            case self::ENTITY_STATE_DIRTY:
                $this->registerDirty($entity);
                return;
            case self::ENTITY_STATE_REMOVED:
                $this->registerRemoved($entity);
                return;
            default: break;
        }
        throw new \InvalidArgumentException(
            "State must be one of ENTITY_STATE_NEW, ENTITY_STATE_CLEAN, ENTITY_STATE_DIRTY, ENTITY_STATE_REMOVED."
        );
    }

    /**
     * @param Entity $entity
     */
    public function unregisterEntity(Entity $entity) {
        $className = get_class($entity);
        $id = $entity->getID();
        $objHash = spl_object_hash($entity);
        unset(
            $this->newEntities[$className][$objHash],
            $this->cleanEntities[$className][$id],
            $this->dirtyEntities[$className][$id],
            $this->removedEntities[$className][$id]
        );

    }

    public function rollback() {
        //Look for entities in dirty / removed that had initially
        //been registered as clean and then moved to dirty / removed through changes.
        //Reset such entities to their initial state cloned on registration
        foreach($this->dirtyEntities as $className => &$entityArr) {
            foreach($entityArr as $id => $obj) {
                if(array_key_exists($id,$this->cleanEntitiesInit[$className])) {
                    $oldState = $this->cleanEntitiesInit[$className][$id];
                    //Initially stored by reference,
                    //So this resets the referenced entity to init state
                    //And then deletes the reference in the array
                    $obj = $entityArr[$id];
                    $obj = $oldState;
                    unset($entityArr[$id]);
                    $this->cleanEntities[$className][$id] = $obj;
                }
            }
        }
        unset($entityArr);

        foreach($this->removedEntities as $className => &$entityArr2) {
            foreach($entityArr2 as $id => $obj) {
                if(array_key_exists($id,$this->cleanEntitiesInit[$className])) {
                    $oldState = $this->cleanEntitiesInit[$className][$id];
                    //Initially stored by reference,
                    //So this resets the referenced entity to init state
                    //And then deletes the reference in the array
                    $obj = $entityArr2[$id];
                    $obj = $oldState;
                    unset($entityArr2[$id]);
                    $this->cleanEntities[$className][$id] = $obj;
                }
            }
        }
        unset($entityArr2);
    }

    public function clear() {
        $this->newEntities = [];
        $this->cleanEntitiesInit = [];
        $this->cleanEntities = [];
        $this->dirtyEntities = [];
        $this->removedEntities = [];
    }

    public function commit() {
        foreach($this->newEntities as $className => $objArr) {
            foreach($objArr as $entity) {
                $this->storage->create($entity);
            }
        }
        foreach($this->dirtyEntities as $className => $objArr) {
            foreach($objArr as $id => $entity) {
                $this->storage->update($entity);
            }
        }
        foreach($this->removedEntities as $className => $objArr) {
            foreach($objArr as $id => $entity) {
                $this->storage->delete($entity);
            }
        }
    }


}