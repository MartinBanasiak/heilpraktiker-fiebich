<?php
namespace DynCom\dc\dcShop\interfaces;

/**
 * Interface TextProviderInterface
 */
interface TextProviderInterface {

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getText( $name );

    /**
     * @param $name
     *
     * @return mixed
     */
    public function printText( $name );
}