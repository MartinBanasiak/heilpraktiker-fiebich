<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.02.2017
 * Time: 12:09
 */

namespace DynCom\dc\pipeline;


class GenericPipeline implements Pipeline, PipelineStage
{
    protected $stages = [];

    public function __construct($stages = [])
    {
        $validStages = [];
        foreach ($stages as $stage) {
            if ($stage instanceof PipelineStage) {
                $validStages[] = $stage;
            }
        }
        $this->stages = $validStages;
    }

    /**
     * @param PipelineStage $stage
     * @param int|null $ordinal
     * @return Pipeline
     */
    public function addStage(PipelineStage $stage)
    {
        $newStages = $this->stages;
        $newStages[] = $stage;
        return new GenericPipeline($newStages);
    }

    /**
     * @param $payload
     * @return $payload
     */
    public function process($payload)
    {
        foreach ($this->stages as $stage) {
            $payload = $stage->handlePayload($payload);
        }
        return $payload;
    }

    /**
     * @param $payload
     * @return $payload
     */
    public function handlePayload($payload)
    {
        $this->process($payload);
    }

    public function getHash()
    {
        $str = '';
        foreach ($this->stages as $stage) {
            $class = get_class($stage);
            if ($stage instanceof ClosurePipelineStage) {
                $str .= '.' . $stage->getHash();
            } else {
                $str .= '.' . $class;
            }
        }
        return md5($str);
    }

}