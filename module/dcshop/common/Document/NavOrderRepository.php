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
 * Time: 11:21
 */
class NavOrderRepository implements DocumentRepository
{

    use documentRepositoryTrait;

    /**
     * NavOrderRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param NavOrderConfig $config
     * @param NavOrderLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param NavOrderCollection $collection
     * @param bool $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, NavOrderConfig $config, NavOrderLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, NavOrderCollection $collection, $cacheAll = FALSE)
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
        return $this->_setLinesForNavOrderDocument($instance);
    }

    /**
     * @param InvoiceDocument $instance
     * @return bool
     */
    protected function _setLinesForNavOrderDocument(NavOrderDocument $instance)
    {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return NavOrderDocument
     */
    public function getNullObject()
    {
        return new NavOrderDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }


    public function getNavOrderByDate($company, $customerNo, $dateFrom, $dateTo, $offset = 0, $limit = 0)
    {

        $criteria = [
            [
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
                ['order_date', '>=', $dateFrom],
                ['order_date', '<=', $dateTo]
            ]

        ];

        $navOrders = $this->findByCriteria($criteria, $offset, $limit);
        return $navOrders;
    }

    public function getNavOrderByNoAndReference($company, $customerNo, $number)
    {

        $criteria = [
            [
                ['no', '=', $number],
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
            ],
            [
                ['webshop_order_no', '=', $number],
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
            ],
            [
                ['your_reference', '=', $number],
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
            ],

        ];

        $navOrders = $this->findByCriteria($criteria);
        return $navOrders;
    }

    public function getNavOrderByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $number)
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
        $navOrders = $this->findByCriteria($criteria);
        return $navOrders;
    }

    public function getNavOrders($company, $customerNo, $offset = 0, $limit = 0)
    {

        $criteria = [
            [
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
            ]

        ];

        $navOrders = $this->findByCriteria($criteria, $offset, $limit);
        return $navOrders;
    }

    public function getNavOrderByNo($company, $customerNo, $number)
    {

        $criteria = [
            [
                ['no', '=', $number],
                ['company', '=', $company],
                ['sell_to_customer_no', '=', $customerNo],
            ],

        ];

        $navOrders = $this->findByCriteria($criteria);
        return $navOrders;
    }


    public function getNavOrderByWebshopOrderNo($company, $customerNo, $orderNumber)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
                ['webshop_order_no', '=', $orderNumber],
            ]

        ];

        $navOrders = $this->findByCriteria($criteria);
        return $navOrders;
    }

}