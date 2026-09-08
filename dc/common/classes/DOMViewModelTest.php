<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\ViewModel;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/23/2015
 * Time: 11:22 PM
 */

class DOMViewModelTest  implements ViewModel {

    /**
     * @return array
     */
    public function getData() {
        $tmpDoc = new \DOMDocument();
        $el = $tmpDoc->createElement('a');
        $el->setAttribute('href','#inserted-field3');
        $el->setAttribute('id','inserted-field3');
        $txtNode1 = $tmpDoc->createTextNode('inserted-field3');
        $el->appendChild($txtNode1);
        $domDoc = new \DOMDocument();
        $span = $domDoc->createElement('span');
        $span->setAttribute('id','inserted-field4');
        $domDoc->appendChild($span);
        $arr = [
            'insertField1' => 'inserted-field1',
            'insertField2' => '<span id="inserted-field2">TestInsertion2</span>',
            'insertField3' => $el,
            'insertField4' => $domDoc
        ];
        return $arr;
    }
}