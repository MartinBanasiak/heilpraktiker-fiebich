<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;

/**
 * Class SubscriptionHeaderRepository
 */
class SubscriptionHeaderRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @param PDOQueryWrapper $db
     * @param SubscriptionHeaderConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param SubscriptionHeaderCollection $collection
     * @param $cacheAll
     */
    public function __construct( PDOQueryWrapper $db, SubscriptionHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService, SubscriptionHeaderCollection $collection, $cacheAll = false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
    }

    /**
     * @return SubscriptionHeader
     */
    public function getNullObject() {
        return new SubscriptionHeader($this->config);
    }

    /**
     * @param WebshopItemInterface $item
     * @return mixed|SubscriptionHeader
     * @throws \ErrorException
     */
    public function getOrderableSequenceSubscriptionForWebshopItem(WebshopItemInterface $item) {
        $currDate = new \DateTime();
        $currMySQLDate = $currDate->format('Y-m-d H:i:s');
        $this->db
            ->select('id')
            ->from('shop_subscription_header')
            ->where('orderable','=',true)
            ->andWhere('item_no_subscription_item','=',$item->getItemNo())
            ->enterParentheses('AND')
                ->where('shop_code','=',$item->getShopCode())
                ->orWhere('all_shops','=',true)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('language_code','=',$item->getLanguageCode())
                ->orWhere('all_languages','=',true)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('valid_from','<=',$currMySQLDate)
                ->orWhere('valid_from','IS NULL')
                ->orWhere('valid_from','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('orderable_from','<=',$currMySQLDate)
                ->orWhere('orderable_from','IS NULL')
                ->orWhere('orderable_from','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('orderable_to','>=',$currMySQLDate)
                ->orWhere('orderable_to','IS NULL')
                ->orWhere('orderable_to','=',0)
            ->leaveParentheses()
            ->setConstructedQuery()->doQuery();
        if($this->db->getNoOfReturnedRows() === 1) {
            return $this->findByID($this->db->getResultArray()[0]['id']);
        }
        return $this->getNullObject();
    }

    /**
     * @param WebshopItemInterface $item
     * @return SubscriptionHeaderCollection
     * @throws \ErrorException
     */
    public function getOrderableSubscriptionOrderOptionsForWebshopItem(WebshopItemInterface $item) {
        $currDate = new \DateTime();
        $currMySQLDate = $currDate->format('Y-m-d H:i:s');
        $collection = new SubscriptionHeaderCollection($this->config,$this->criteriaValidationService);
        $this->db
                ->select('sh.*')
                ->from('shop_subscription_item_link','sil')
                ->join('left','shop_subscription_header','sh')
                    ->on('sh.company','=','sil.company')
                    ->andOn('sh.code', '=', 'sil.subscription_code')
                    ->andOn('sh.type', '=', 0)
                    ->andOn('sh.orderable', '=', 1)
                    ->enterParentheses('AND')
                        ->on('sh.orderable_from','<=',$currMySQLDate,true)
                        ->orOn('sh.orderable_from','IS NULL')
                        ->orOn('sh.orderable_from','=',0)
                    ->leaveParentheses()
                    ->enterParentheses('AND')
                        ->on('sh.orderable_to','>=',$currMySQLDate,true)
                        ->orOn('sh.orderable_to','IS NULL')
                        ->orOn('sh.orderable_to','=',0)
                    ->leaveParentheses()
                    ->enterParentheses('AND')
                        ->on('sh.shop_code','=',$item->getShopCode(),true)
                        ->orOn('sh.all_shops','=',1)
                    ->leaveParentheses()
                    ->enterParentheses('AND')
                        ->on('sh.language_code','=',$item->getLanguageCode(),true)
                        ->orOn('sh.all_languages','=',1)
                    ->leaveParentheses()
                ->where('sil.company','=',$item->getCompany())
                ->andWhere('sil.link_to','=',0)
                ->andWhere('sil.item_no','=',$item->getItemNo())
                ->andWhere('sil.variant_code','=',$item->getVariantCode())
            ->setConstructedQuery()->doQuery();
        if(!($this->db->getNoOfReturnedRows() > 0)) { return $collection;};
        $result = $this->db->getResultArray();
        foreach($result as $headerArr) {
            $header = new SubscriptionHeader($this->config);
            $header->mapFromArray($headerArr);
            $collection->add($header);
        }
        return $collection;
    }

    /**
     * @return SubscriptionHeaderConfig
     */
    public function getConfig() {
        return $this->config;
    }



    /**
     * @param SubscriptionCustomerLink $link
     */
    public function decorateSubscriptionCustomerLinkWithHeader(SubscriptionCustomerLink $link) {
        $company = $link->company;
        $code = $link->subscription_code;
        $header = $this->findByAltPrimary(['company' => $company, 'code' => $code]);
        $link->setSubscriptionHeader($header);
    }
}