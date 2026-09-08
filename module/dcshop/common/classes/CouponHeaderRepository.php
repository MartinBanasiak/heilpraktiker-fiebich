<?php

namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBModelInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class CouponHeaderRepository
 */
class CouponHeaderRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @var CouponLineRepository
     */
    protected $lineRepository;

    /**
     * CouponHeaderRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CouponHeaderConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CouponHeaderCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, CouponLineRepository $lineRepository, CouponHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService, CouponHeaderCollection $collection, $cacheAll = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
        $this->lineRepository = $lineRepository;
    }

    public function setLinesForDocument(CouponHeader $header)
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
        if(!($instance instanceof CouponHeader)) {
            return FALSE;
        }
        $lines = $instance->getLines();
        foreach($lines as $line) {
            $this->lineRepository->updateSingle($line);
        }
        return $this->_updateSingle($instance);
    }



    /**
     * @return CouponHeader
     */
    public function getNullObject()
    {
        return new CouponHeader($this->config);
    }

}