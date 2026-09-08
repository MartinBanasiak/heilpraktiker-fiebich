<?php
namespace DynCom\dc\dcShop\Document;

use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\dcShop\classes\Customer;
use DynCom\dc\dcShop\interfaces\DocumentRepository;
use DynCom\dc\dcShop\interfaces\GenericDocumentInterface;
use DynCom\dc\dcShop\traits\documentRepositoryTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 19.01.2015
 * Time: 14:08
 */
class WebshopOrderRepository implements DocumentRepository
{

    use documentRepositoryTrait;

    /**
     * WebshopOrderRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param WebshopOrderConfig $config
     * @param WebshopOrderLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param WebshopOrderCollection $collection
     * @param bool $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, WebshopOrderConfig $config, WebshopOrderLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, WebshopOrderCollection $collection, $cacheAll = FALSE)
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
        return $this->_setLinesForWebshopOrderDocument($instance);
    }

    /**
     * @param WebshopOrderDocument $instance
     * @return bool
     */
    protected function _setLinesForWebshopOrderDocument(WebshopOrderDocument $instance)
    {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return WebshopOrderDocument
     */
    public function getNullObject()
    {
        return new WebshopOrderDocument($this->config, $this->linesConfig, $this->criteriaValidationService);
    }

    public function getOrderByDate($company, $shopCode, $languageCode, $customerNo, $orderDateFrom, $orderDateTo, $offset = 0, $limit = 0, $orderBy = '')
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
                ['order_date', '>=', $orderDateFrom],
                ['order_date', '<=', $orderDateTo]
            ]

        ];

        $webshopOrders = $this->findByCriteria($criteria, $offset, $limit, $orderBy);

        return $webshopOrders;
    }


    public function getOrderByDateAndOrderNoAndReference($company, $shopCode, $languageCode, $customerNo, $orderDateFrom, $orderDateTo, $orderNumber, $offset = 0, $limit = 0, $orderBy = '')
    {

        $criteria = [
            [
                ['order_no', '=', $orderNumber],
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
                ['order_date', '>=', $orderDateFrom],
                ['order_date', '<=', $orderDateTo],
            ],
            [
                ['your_reference', '=', $orderNumber],
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
                ['order_date', '>=', $orderDateFrom],
                ['order_date', '<=', $orderDateTo],
            ],
            [
                ['your_comment', '=', $orderNumber],
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
                ['order_date', '>=', $orderDateFrom],
                ['order_date', '<=', $orderDateTo],
            ],

        ];

        $webshopOrders = $this->findByCriteria($criteria, $offset, $limit, $orderBy);

        return $webshopOrders;
    }

    public function getOrderByOrderNoAndReference($company, $shopCode, $languageCode, $customerNo, $orderNumber, $offset = 0, $limit = 0, $orderBy = '')
    {

        $criteria = [
            [
                ['order_no', '=', $orderNumber],
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
            ],
            [
                ['your_reference', '=', $orderNumber],
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
            ],
            [
                ['your_comment', '=', $orderNumber],
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
            ],

        ];


        $webshopOrders = $this->findByCriteria($criteria, $offset, $limit, $orderBy);
        return $webshopOrders;
    }

    public function getOrderByOrderNo($company, $shopCode, $languageCode, $customerNo, $orderNumber, $offset = 0, $limit = 0, $orderBy = '')
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
                ['order_no', '=', $orderNumber],
            ]

        ];

        $webshopOrders = $this->findByCriteria($criteria, $offset, $limit, $orderBy);
        return $webshopOrders;
    }

    public function getOrders($company, $shopCode, $languageCode, $customerNo, $offset = 0, $limit = 0, $orderBy = '')
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $languageCode],
                ['customer_no', '=', $customerNo],
            ]

        ];
        $webshopOrders = $this->findByCriteria($criteria, $offset, $limit, $orderBy);
        return $webshopOrders;
    }

}