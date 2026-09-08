<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class InvoiceDiscountRepository
 */
class InvoiceDiscountRepository implements Repository{

    use genericRepositoryTrait;

    /**
     * InvoiceDiscountRepository constructor.
     * @param PDOQueryWrapper $db
     * @param InvoiceDiscountConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param InvoiceDiscountCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, InvoiceDiscountConfig $config, CriteriaHelperInterface $criteriaValidationService, InvoiceDiscountCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return InvoiceDiscount
     */
    public function getNullObject() {
        return new InvoiceDiscount($this->config);
    }

    /**
     * @param $company
     * @param $invoiceDiscountCode
     * @param $invoiceDiscAllowedSubtotal
     * @return float
     */
    public function getBestDiscountPercentForOrder($company, $invoiceDiscountCode, $invoiceDiscAllowedSubtotal) {
        $db = $this->db;
        $querySuccess =
         $db->select('discount')
            ->from('shop_invoice_discount')
            ->where('company','=',strip_tags($company))
            ->andWhere('invoice_discount_code','=',strip_tags($invoiceDiscountCode))
            ->andWhere('minimum_amount','<=',(float)$invoiceDiscAllowedSubtotal)
            ->orderBy('minimum_amount','desc')
            ->setConstructedQuery()->doQuery();
        if(!$querySuccess) {
            return 0.00;
        }
        $valArr = $db->getResultArray()[0];
        return (float)$valArr['discount'];
    }

}