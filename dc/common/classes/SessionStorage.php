<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CRUDObjectStorage;
use DynCom\dc\common\interfaces\Entity;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 08.07.2015
 * Time: 01:28
 */
class SessionStorage implements CRUDObjectStorage
{

    protected $namespace;

    protected $storedIDsPerClassName = [];


    /**
     * @param string $namespace
     */
    public function __create($namespace = 'global') {
        $this->namespace = (string)$namespace;
        $this->hydrateFromSession();
    }

    protected function hydrateFromSession() {
        if( !array_key_exists($this->namespace,$_SESSION['SessionObjectStorage']) ||
            !is_array($_SESSION['SessionObjectStorage'][$this->namespace]) ||
            !array_key_exists('storedIDsPerClassName',$_SESSION['SessionObjectStorage'][$this->namespace]) ||
            !is_array($_SESSION['SessionObjectStorage'][$this->namespace]['storedIDsPerClassName'])) return;
        $this->storedIDsPerClassName = $_SESSION['SessionObjectStorage'][$this->namespace]['storedIDsPerClassName'];
    }

    /**
     * @param Entity $entity
     */
    public function create(Entity $entity) {
        $id = $entity->getID();
        if($id > 0) {
            throw new \InvalidArgumentException(
                'Entities with ID > 0 cannot be created.'
            );
        }
        $className = get_class($entity);
        $newID = 1;
        if(array_key_exists($className,$this->storedIDsPerClassName) && is_array($this->storedIDsPerClassName[$className])) {
            $newID = ((int)max($this->storedIDsPerClassName[$className])) + 1;
        }
        $entityPropArray = $entity->getAllFieldsAsArray();
        $entityPropArray['id'] = $newID;
        $entity->mapFromArray($entityPropArray);
        $_SESSION['SessionObjectStorage'][$this->namespace][$className][$newID] = serialize($entity);
    }

    /**
     * @param Entity $entity
     */
    public function replace(Entity $entity)
    {
        $id = $entity->getID();
        if (!$id) {
            throw new \InvalidArgumentException(
                'Entities without ID > 0cannot be replaced.'
            );
        }

        $className = get_class($entity);
        if (!array_key_exists($className,$this->storedIDsPerClassName) || !is_array($this->storedIDsPerClassName[$className])) {
            throw new \InvalidArgumentException(
                "No objects for class $className are in SessionStorage of namespace $this->namespace"
            );
        }

        if(!array_key_exists($id,$_SESSION['SessionObjectStorage'][$this->namespace][$className])) {
            throw new \InvalidArgumentException(
              "No object of class $className and with ID $id exists in SessionStorage of namespace $this->namespace"
            );
        }

        $_SESSION['SessionObjectStorage'][$this->namespace][$className][$id] = serialize($entity);

    }

    /**
     * @param Entity $entity
     */
    public function update(Entity $entity) {
        $id = $entity->getID();
        if (!$id) {
            throw new \InvalidArgumentException(
                'Entities without ID > 0 cannot be updated.'
            );
        }

        $className = get_class($entity);
        if (!array_key_exists($className,$this->storedIDsPerClassName) || !is_array($this->storedIDsPerClassName[$className])) {
            throw new \InvalidArgumentException(
                "No objects for class $className are in SessionStorage of namespace $this->namespace"
            );
        }

        if(!array_key_exists($id,$_SESSION['SessionObjectStorage'][$this->namespace][$className])) {
            throw new \InvalidArgumentException(
                "No object of class $className and with ID $id exists in SessionStorage of namespace $this->namespace"
            );
        }

        $altPrimaryFields = $entity->getAltPrimaryFieldNames();
        $entityPropArr = $entity->getAllFieldsAsArray();
        foreach($altPrimaryFields as $primaryField) {
            if(array_key_exists($primaryField,$altPrimaryFields)) unset($entityPropArr[$primaryField]);
        }

        $storedEntity = unserialize($_SESSION['SessionObjectStorage'][$this->namespace][$className][$id]);
        $storedEntity->mapFromArray($storedEntity);

        $_SESSION['SessionObjectStorage'][$this->namespace][$className][$id] = serialize($storedEntity);

    }

    /**
     * @param Entity $entity
     */
    public function delete(Entity $entity) {
        $id = $entity->getID();
        if (!($id > 0)) {
            throw new \InvalidArgumentException(
                'Entities without ID cannot be deleted.'
            );
        }

        $className = get_class($entity);
        if (!array_key_exists($className,$this->storedIDsPerClassName) || !is_array($this->storedIDsPerClassName[$className])) {
            throw new \InvalidArgumentException(
                "No objects for class $className are in SessionStorage of namespace $this->namespace"
            );
        }

        if(!array_key_exists($id,$_SESSION['SessionObjectStorage'][$this->namespace][$className])) {
            throw new \InvalidArgumentException(
                "No object of class $className and with ID $id exists in SessionStorage of namespace $this->namespace"
            );
        }

        $sessionObjVar = $_SESSION['SessionObjectStorage'][$this->namespace][$className][$id];
        unset($_SESSION['SessionObjectStorage'][$this->namespace][$className][$id],$sessionObjVar);

    }

    /**
     * @param $id
     * @param $className
     * @param string $namespace
     * @return bool|string
     */
    public function getByID($id, $className, $namespace = 'global') {
        $return = $_SESSION['SessionObjectStorage'][$namespace][$className][(int)$id] ?
            serialize($_SESSION['SessionObjectStorage'][$this->namespace][$className][(int)$id]) : false;
        return $return;
    }

    /**
     * @param $id
     * @param $className
     * @param string $namespace
     * @return bool|string
     */
    public function read($id, $className, $namespace = 'global') {
        return $this->getByID($id,$className,$namespace);
    }

}