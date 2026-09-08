<?php
namespace DynCom\dc\dcShop\Document;

use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\interfaces\DocumentRepository;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 15.01.2015
 * Time: 11:23
 */
class ShipmentRepository implements DocumentRepository
{

    use documentRepositoryTrait;

    /**
     * ShipmentRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param ShipmentConfig $config
     * @param ShipmentLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShipmentCollection $collection
     * @param bool $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, ShipmentConfig $config, ShipmentLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, ShipmentCollection $collection, $cacheAll = FALSE)
    {
        $this->db = $db;
        $this->config = $config;
        $this->lineRepository = $lineRepository;
        $this->linesConfig = $this->lineRepository->getConfig();
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->criteriaValidationService = $criteriaValidationService;

        $this->cacheAll = $cacheAll;
    }

    /**
     * @param GenericDocumentInterface $instance
     * @return bool
     */
    public function setLinesForDocument(GenericDocumentInterface $instance)
    {
        return $this->_setLinesForShipmentDocument($instance);
    }

    /**
     * @param ShipmentDocument $instance
     * @return bool
     */
    protected function _setLinesForShipmentDocument(ShipmentDocument $instance)
    {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return ShipmentDocument
     */
    public function getNullObject()
    {
        return new ShipmentDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

    public function getShipmentByDate($company, $customerNo, $dateFrom, $dateTo, $offset = 0, $limit = 0)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
                ['posting_date', '>=', $dateFrom],
                ['posting_date', '<=', $dateTo]
            ]

        ];
        $shipments = $this->findByCriteria($criteria, $offset, $limit);
        return $shipments;
    }

    public function getShipmentByNoAndReference($company, $customerNo, $number)
    {
        $criteria = [
            [
                ['no', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
            ],
            [
                ['webshop_order_no', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
            ],
            [
                ['your_reference', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
            ],

        ];
        $shipments = $this->findByCriteria($criteria);
        return $shipments;
    }


    public function getShipmentByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $number)
    {
        $criteria = [
            [
                ['no', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
                ['posting_date', '>=', $dateFrom],
                ['posting_date', '<=', $dateTo],
            ],
            [
                ['webshop_order_no', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
                ['posting_date', '>=', $dateFrom],
                ['posting_date', '<=', $dateTo],

            ],
            [
                ['your_reference', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
                ['posting_date', '>=', $dateFrom],
                ['posting_date', '<=', $dateTo],
            ],

        ];
        $shipments = $this->findByCriteria($criteria);
        return $shipments;
    }

    public function getShipmentByWebshopOrderNo($company, $customerNo, $orderNumber)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
                ['webshop_order_no', '=', $orderNumber],
            ]

        ];
        $shipments = $this->findByCriteria($criteria);
        return $shipments;
    }


    public function getShipments($company, $customerNo, $offset = 0, $limit = 0)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
            ]

        ];
        $shipments = $this->findByCriteria($criteria, $offset, $limit);
        return $shipments;
    }

    public function getShipmentByNo($company, $customerNo, $number)
    {
        $criteria = [
            [
                ['no', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
            ],

        ];
        $shipments = $this->findByCriteria($criteria);
        return $shipments;
    }
}