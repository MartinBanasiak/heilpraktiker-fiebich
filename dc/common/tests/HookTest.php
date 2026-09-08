<?php
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 01.09.2015
 * Time: 10:39
 */

if(!is_callable('DcAutoloader')) {
    function DcAutoloader( $objectName ) {

        $dirArr = array(
            /* Base folders */
            'C:/xampp/htdocs/dc/common/abstracts/',
            'C:/xampp/htdocs/dc/common/classes/',
            'C:/xampp/htdocs/dc/common/traits/',
            'C:/xampp/htdocs/dc/common/interfaces/',
            /* Shop folders */
            'C:/xampp/htdocs/module/dcshop/common/abstracts/',
            'C:/xampp/htdocs/module/dcshop/common/classes/',
            'C:/xampp/htdocs/module/dcshop/common/traits/',
            'C:/xampp/htdocs/module/dcshop/common/interfaces/',
            /* RMA folders */
            'C:/xampp/htdocs/module/dcshop/rma/classes/',
            /* Subscription folders */
            'C:/xampp/htdocs/module/dcshop/subscriptions/classes/',
            'C:/xampp/htdocs/module/dcshop/interfaces/classes/'

        );

        foreach ($dirArr as $dir) {
            if (!is_dir($dir)) {
                        echo "
                not a dir: " . $dir . " CURRDIR: " . __DIR__;

            }
            $path = $dir . $objectName . '.php';
            if (file_exists(realpath($path))) {
                /** echo "<br>FILE FOUND IN $path <br/><br/>"; **/

                include(realpath($path));
                return;
            } else {
                /** echo "<br>NO FILE IN $path <br/><br/>"; **/
            }
        }
    }

    spl_autoload_register('DcAutoloader');
}

class A implements Observer {

    public function __construct() {
        Hook::registerEventListener($this,'B.changed');
    }

    public function notify($eventName, $data)
    {
        echo 'Event Name ' . $eventName . ' called with data ' . print_r($data,true);
    }

}

class B {

    use hookableTrait;

    public function __construct() {
        $this->prop3 = (object)$this->prop3;
    }

    private $prop1 = 'initProp1';
    private $prop2 = 0;
    private $prop3 = ['testObjectProp' => 'testObjectPropValue'];

    public function setProp1($prop1) {
        $this->prop1 = $prop1;
        $this->updateHooks('changed',$this);
    }

    public function setProp2($prop2) {
        $this->prop2 = $prop2;
        $this->updateHooks('changed',$this);
    }

    public function setProp3($prop3) {
        $this->prop3 = $prop3;
        $this->updateHooks('changed',$this);
    }

}


class C extends B {
    private $prop4 = 'initProp4';
    public function setProp4($prop4) {
        $this->prop4 = $prop4;
        $this->updateHooks('changed',$this);
    }
}

$observer = new A();
$observed = new B();
$observedExt = new C();
$observed->setProp1('newProp1Value');
$observed->setProp2(1);
$observed->setProp3((object)['testObjectNewProp' => 'testObjectNewPropValue']);
$observedExt->setProp4('newProp4Value');