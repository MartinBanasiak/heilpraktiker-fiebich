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
 * Time: 11:20
 */
class CreditMemoRepository implements DocumentRepository
{

    use documentRepositoryTrait;

    /**
     * InvoiceRepository constructor.
     * @param GenericDBQueryWrapperInterface $db
     * @param CreditMemoConfig $config
     * @param CreditMemoLineRepository $lineRepository
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CreditMemoCollection $collection
     * @param bool $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, CreditMemoConfig $config, CreditMemoLineRepository $lineRepository, CriteriaHelperInterface $criteriaValidationService, CreditMemoCollection $collection, $cacheAll = FALSE)
    {
        $this->db = $db;
        $this->config = $config;
        $this->lineRepository = $lineRepository;
        $this->lineConfig = $this->lineRepository->getConfig();
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
        return $this->_setLinesForInvoiceDocument($instance);
    }

    /**
     * @param CreditMemoDocument $instance
     * @return bool
     */
    protected function _setLinesForInvoiceDocument(CreditMemoDocument $instance)
    {
        return $this->_setLinesForDocument($instance);
    }

    /**
     * @return CreditMemoDocument
     */
    public function getNullObject()
    {
        return new CreditMemoDocument($this->config, $this->lineConfig, $this->criteriaValidationService);
    }


    public function getCreditMemoByDate($company, $customerNo, $dateFrom, $dateTo, $offset = 0, $limit = 0)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
                ['posting_date', '>=', $dateFrom],
                ['posting_date', '<=', $dateTo]
            ]

        ];

        $crMemo = $this->findByCriteria($criteria, $offset, $limit);

        return $crMemo;
    }

    public function getCreditMemoByNoAndReference($company, $customerNo, $number)
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

        $crMemo = $this->findByCriteria($criteria);
        return $crMemo;
    }


    public function getCreditMemoByNoAndDateAndReference($company, $customerNo, $dateFrom, $dateTo, $number)
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
        $crMemo = $this->findByCriteria($criteria);
        return $crMemo;
    }


    public function getCreditMemoByWebshopOrderNo($company, $customerNo, $orderNumber)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
                ['webshop_order_no', '=', $orderNumber],
            ]

        ];

        $crMemo = $this->findByCriteria($criteria);
        return $crMemo;
    }

    public function getCreditMemos($company, $customerNo, $offset = 0, $limit = 0)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
            ]
        ];

        $crMemo = $this->findByCriteria($criteria, $offset, $limit);
        return $crMemo;
    }


    public function getCreditMemoByNo($company, $customerNo, $number)
    {
        $criteria = [
            [
                ['no', '=', $number],
                ['company', '=', $company],
                ['bill_to_customer_no', '=', $customerNo],
            ],

        ];

        $crMemo = $this->findByCriteria($criteria);
        return $crMemo;
    }

}