<?php
namespace DynCom\dc\common\traits;

use DynCom\dc\common\classes\Hook;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.07.2015
 * Time: 11:20
 */
trait hookableTrait
{

    /**
     * @param $eventName
     * @param $data
     */
    protected function updateHooks($eventName, $data)
    {
        $parents = class_parents($this);
        $interfaces = class_implements($this);
        $className = get_class($this);
        $notificationTargets = [$className];
        $namespace = '';
        foreach ($parents as $parent) {
            $notificationTargets[] = $parent;
        }
        foreach ($interfaces as $interface) {
            $notificationTargets[] = $interface;
        }
        foreach ($notificationTargets as $target) {
            Hook::update($eventName, $data, $target);
        }
        Hook::update($eventName, $data);
    }

}