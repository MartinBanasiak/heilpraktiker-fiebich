<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:14
 */

namespace DynCom\dc\pipeline;


class ClosurePipelineStage implements PipelineStage
{
    protected $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param $payload
     * @return $payload
     */
    public function handlePayload($payload)
    {
        return $this->callable($payload);
    }

    public function getHash()
    {
        if (is_string($this->callable)) {
            return $this->callable;
        }
        return md5(var_export($this->callable));
    }


}