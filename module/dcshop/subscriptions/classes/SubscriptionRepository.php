<?php
namespace DynCom\dc\dcShop\subscriptions\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\classes\WebshopItem;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/24/2015
 * Time: 1:52 PM
 */

class SubscriptionRepository {

    protected $db;
    protected $headerRepository;
    protected $stepRepository;
    protected $itemRepository;
    protected $customerLinkRepository;

    protected $checkedCustomers;
    protected $checkedItems;


    /**
     * SubscriptionRepository constructor.
     * @param PDOQueryWrapper $db
     * @param SubscriptionHeaderRepository $headerRepository
     * @param SubscriptionSequenceStepRepository $stepRepository
     * @param SubscriptionItemLinkRepository $itemRepository
     * @param SubscriptionCustomerLinkRepository $customerLinkRepository
     */
    public function __construct(PDOQueryWrapper $db, SubscriptionHeaderRepository $headerRepository, SubscriptionSequenceStepRepository $stepRepository,
                                SubscriptionItemLinkRepository $itemRepository, SubscriptionCustomerLinkRepository $customerLinkRepository) {
        $this->db = $db;
        $this->headerRepository = $headerRepository;
        $this->stepRepository = $stepRepository;
        $this->itemRepository = $itemRepository;
        $this->customerLinkRepository = $customerLinkRepository;
    }

    /**
     * @param WebshopItem $item
     * @return bool
     */
    public function hasItemSubscriptionOrderOption(WebshopItem $item) {
        $currDate = new \DateTime();
        $currMySQLDate = $currDate->format('Y-m-d H:i:s');
        $query = <<<SQL
            SELECT sil.id
            FROM
              shop_subscription_item_link sil
            LEFT JOIN
              shop_subscription_header ssh
              ON
                sil.subscription_code = ssh.code
            WHERE
                  ssh.active = 1
              AND ssh.orderable = 1
              AND (
                    ssh.shop_code = '{$item->getShopCode()}'
                OR  ssh.all_shops = 1
              )
              AND (
                    ssh.language_code = '{$item->getLanguageCode()}'
                OR  ssh.all_languages = 1
              )
              AND (
                    ssh.active_from <= '{$currMySQLDate}'
                OR  ssh.active_from IS NULL
              )
              AND (
                    ssh.active_to >= '{$currMySQLDate}'
                OR  ssh.active_to IS NULL
              )
              AND (
                    ssh.orderable_from <= '{$currMySQLDate}'
                OR ssh.orderable_from IS NULL
              )
              AND sil.item_no = '{$item->getItemNo()}'
SQL;
        $this->db->setQuery($query)->doQuery();
        $numRows = $this->db->getNoOfReturnedRows();
        return (isset($numRows) && $numRows > 0);
    }

    /**
     * @param WebshopItem $item
     * @return bool
     */
    public function isItemSubscriptionSequenceItem(WebshopItem $item) {
        $currDate = new \DateTime();
        $currMySQLDate = $currDate->format('Y-m-d H:i:s');
        $this->db
            ->select('id')
            ->from('shop_subscription_header')
            ->where('active','=',true)
            ->andWhere('orderable','=',true)
            ->andWhere('item_no_subscription_item','=',$item->item_no)
            ->enterParentheses('AND')
                ->where('shop_code','=',$item->shop_code)
                ->orWhere('all_shops','=',true)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('language_code','=',$item->language_code)
                ->orWhere('all_languages','=',true)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('active_from','<=',$currMySQLDate)
                ->orWhere('active_from','IS NULL')
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('active_to','>=',$currMySQLDate)
                ->orWhere('active_to','IS NULL')
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('orderable_from','<=',$currMySQLDate)
                ->orWhere('orderable_from','IS NULL')
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('orderable_to','>=',$currMySQLDate)
                ->orWhere('orderable_to','IS NULL')
            ->leaveParentheses()
          ->setConstructedQuery()->doQuery();
        $numRows = $this->db->getNoOfReturnedRows();
        return (isset($numRows) && $numRows > 0);
    }

}