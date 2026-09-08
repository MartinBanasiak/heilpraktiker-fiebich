<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:06
 */

namespace DynCom\dc\pipeline;


interface PipelineStage
{
    /**
     * @param $payload
     * @return $payload
     */
    public function handlePayload($payload);

}