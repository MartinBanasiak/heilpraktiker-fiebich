<?php

namespace DynCom\dc\dcShop\ShippingOptions;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class ShippingZoneRepository
 */
class ShippingZoneRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @var ShippingZoneLineRepository
     */
    protected $lineRepository;

    /**
     * ShippingZoneRepository constructor.
     * @param PDOQueryWrapper $db
     * @param ShippingZoneLineRepository $lineRepository
     * @param ShippingZoneConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShippingZoneCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, ShippingZoneLineRepository $lineRepository, ShippingZoneConfig $config, CriteriaHelperInterface $criteriaValidationService, ShippingZoneCollection $collection, $cacheAll = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
        $this->lineRepository = $lineRepository;
    }

    public function setLinesForDocument(ShippingZone $header)
    {
        $linesCriteria = [];
        $linesCriteria[] = [];
        $fieldMappings = $this->config->getLineCriteriaFieldMappings();
        foreach ($fieldMappings as $fieldMapping) {
            $linesFieldName = $fieldMapping[0] ?? '';
            $operator = $fieldMapping[1] ?? '';
            $headerFieldVal = null;
            $headerFieldName = $fieldMapping[2] ?? '';
            if (property_exists($header,$headerFieldName)) {
                $headerFieldVal = $header->$headerFieldName;
            }
            $linesCriteria[0][] = [$linesFieldName,$operator,$headerFieldVal];
        }
        $lines = $this->lineRepository->findByCriteria($linesCriteria);
        $header->setLines($lines);
    }

    public function updateSingle( GenericDBModelInterface $instance ) {
        if(!($instance instanceof ShippingZone)) {
            return FALSE;
        }
        $lines = $instance->getLines();
        foreach($lines as $line) {
            $this->lineRepository->updateSingle($line);
        }
        return $this->_updateSingle($instance);
    }



    /**
     * @return ShippingZone
     */
    public function getNullObject()
    {
        return new ShippingZone($this->config);
    }

}