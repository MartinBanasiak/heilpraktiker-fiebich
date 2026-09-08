<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:04
 */

namespace DynCom\dc\pipeline;


interface Pipeline
{
    /**
     * @param PipelineStage $stage
     * @param int|null $ordinal
     * @return Pipeline
     */
    public function addStage(PipelineStage $stage);

    /**
     * @param $payload
     * @return mixed
     */
    public function process($payload);

    public function getHash();

}