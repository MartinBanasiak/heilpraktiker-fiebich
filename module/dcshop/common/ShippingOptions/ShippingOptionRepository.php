<?php
namespace DynCom\dc\dcShop\ShippingOptions;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;
use DynCom\dc\dcShop\interfaces\UserBasket;

/**
 * Class ShippingOptionRepository
 */
class ShippingOptionRepository implements Repository{

    use genericRepositoryTrait;

    private $specialShippingOptions = [];
    private $activeCampaignCodes = [];
    /**
     * @var ShippingZoneRepository
     */
    private $shippingZoneRepository;
    /**
     * @var ShippingOptionTranslationRepository
     */
    private $shippingOptionTranslationRepository;

    /**
     * ShippingOptionRepository constructor.
     * @param PDOQueryWrapper $db
     * @param ShippingOptionConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param ShippingOptionCollection $collection
     * @param ShippingZoneRepository $shippingZoneRepository
     * @param ShippingOptionTranslationRepository $shippingOptionTranslationRepository
     * @param bool $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, ShippingOptionConfig $config, CriteriaHelperInterface $criteriaValidationService, ShippingOptionCollection $collection, ShippingZoneRepository $shippingZoneRepository, ShippingOptionTranslationRepository $shippingOptionTranslationRepository, $cacheAll = false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
        $this->cacheAll                  = $cacheAll;
        $this->shippingZoneRepository = $shippingZoneRepository;
        $this->shippingOptionTranslationRepository = $shippingOptionTranslationRepository;
    }

    /**
     * @return ShippingOption
     *
     */
    public function getNullObject() {
        return new ShippingOption($this->config);
    }

    /**
     * @param Shop $shop
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     * @throws \Exception
     */
    public function getAllForShop(Shop $shop) {
        $criteria = [
          [
              ['company','=',$shop->company],
              ['shipping_group_code','=',$shop->shipping_group_code]
          ]
        ];
        return $this->findByCriteria($criteria);
    }

    /**
     * @param Shop $shop
     * @param $amount
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     * @throws \Exception
     */
    public function getAllForShopAndAmount(Shop $shop, $amount) {
        $amount = (float)$amount;
        $criteria = [
            [
                ['company','=',$shop->company],
                ['shipping_group_code','=',$shop->shipping_group_code],
                ['amount_to','<=',$amount],
                ['amount_from','>=',$amount]
            ]
        ];
        foreach($this->activeCampaignCodes as $campaignCode) {
            $newDisjunct = $criteria[0];
            $newDisjunct[] = ['campaign_code','=',$campaignCode];
            $criteria[] = $newDisjunct;
        }

        $noOfActiveCampaigns = count($this->activeCampaignCodes);
        /*if($noOfActiveCampaigns === 1) {
            //@TODO: Finalize
        }*/
        return $this->findByCriteria($criteria);
    }

    /**
     * @param Shop $shop
     * @param $lineNo
     * @return mixed
     */
    public function findByShopAndLineNo(Shop $shop, $lineNo) {
        $primary = [
            'company' => $shop->company,
            'shipping_group_code' => $shop->shipping_group_code,
            'line_no' => (int)$lineNo
        ];

        return $this->findByAltPrimary($primary);
    }

    /**
     * @param $primaryArr
     * @return bool
     */
    public function hasEntityByPrimary($primaryArr)
    {
        foreach ($this->collection as $instance) {
            $instanceAltPrimary = array();
            foreach ($primaryArr as $key => $value) {
                $instanceAltPrimary[$key] = $instance->$key;
            }
            if ($instanceAltPrimary === $primaryArr) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $code
     */
    public function addCampaignCode($code)
    {
        if(!in_array($code,$this->activeCampaignCodes)) {
            $this->activeCampaignCodes[] = $code;
        }
    }

    /**
     * @param $code
     */
    public function removeCampaignCode($code)
    {
        if(in_array($code,$this->activeCampaignCodes)) {
            unset($this->activeCampaignCodes[$code]);
        }
    }

    /**
     * @param CurrShopConfiguration $configuration
     * @param UserBasket $basket
     * @param $countryCode
     * @param $postCode
     * @param $coupon
     * @return ShippingOptionCollection
     * @throws \ErrorException
     * @throws \Exception
     */
    public function getAllForOrder(CurrShopConfiguration $configuration, UserBasket $basket, $countryCode, $postCode, $coupon = NULL)
    {
        if (empty($countryCode)) {
            $countryCode = '';
        }

        if (empty($postCode)) {
            $postCode = '';
        }

        $basket->recalculateValues();
        $collection = new ShippingOptionCollection($this->config,$this->criteriaValidationService);
        $company = $configuration->getCompany();
        $shopCode = $configuration->getShopCode();
        $languageCode = $configuration->getShopLanguageCode();
        $shippingGroupCode = $configuration->getShop()->getShippingGroupCode();
        $amount = (float)$basket->getBasketTotal();
        $weight = (float)$basket->getSumItemWeight();
        $sorting = ($configuration->getOrderOptionsSorting() == 0) ? '`shipping_cost`' : '`sorting`';
        $dateTime = new \DateTime('now');
        $date = $dateTime->format('Y-m-d');

        $allowedshippingOptionsIds = $this->getCustomerAllowedShippingOptions($configuration);

        $shippingZoneFilterString = $this->getShippingZoneFilterString($countryCode, $postCode, $company);

        $select = 'shop_shipping_option.id,
                    shop_shipping_option.company,
                    shop_shipping_option.shipping_group_code,
                    shop_shipping_option.line_no,
                    shop_shipping_option.shipping_agent_code,
                    shop_shipping_option.shipping_agent_service_code,
                    shop_shipping_option.weight_from,
                    shop_shipping_option.weight_to,
                    shop_shipping_option.amount_from,
                    shop_shipping_option.amount_to,
                    shop_shipping_option.valid_from,
                    shop_shipping_option.valid_to,
                    shop_shipping_option.exemption,
                    
                    CASE WHEN
                        (SELECT count(id) FROM shipping_option_translation where company = \''.$company.'\' AND shipping_group_code = \''.$shippingGroupCode.'\' AND line_no = shop_shipping_option.line_no AND language_code = \''.$languageCode.'\') > 0
                    THEN
                        (SELECT description FROM shipping_option_translation where company = \''.$company.'\' AND shipping_group_code = \''.$shippingGroupCode.'\' AND line_no = shop_shipping_option.line_no AND language_code = \''.$languageCode.'\')
                    ELSE
                        shop_shipping_option.description 
                    END AS \'description\',  
                                         
                    shop_shipping_option.logo,
                    shop_shipping_option.content,
                    shop_shipping_option.sorting,
                    shop_shipping_option.shipping_class_code,
                    shop_shipping_option.shipping_zone_code,
                    
                    CASE WHEN 
                        shop_shipping_option.exemption > 0 AND shop_shipping_option.exemption <= ' . $amount . ' 
                    THEN 
                        0.00 
                    ELSE 
                        ';
        //$select .= $couponSelect;
        $select .= '
                            shop_shipping_option.shipping_cost
                   ';
       // $select .= $couponSelectEnd;
                    
        $select .= 'END AS \'shipping_cost\',
                    
                    shipping_option_link.campaign_code,
                    shipping_option_link.send_order_mail_text
                    ';
           $this->db
            ->select($select)
            ->from($this->config->getTableName())
            ->join('left','shipping_option_link','shipping_option_link')
               ->on('shipping_option_link.company','=','shop_shipping_option.company')
               ->andOn('shipping_option_link.shop_code', '=', $shopCode, true)
               ->andOn('shipping_option_link.shipping_group_code', '=', 'shop_shipping_option.shipping_group_code')
               ->andOn('shipping_option_link.line_no', '=', 'shop_shipping_option.line_no');

           if (null !== $coupon && (int)$coupon['value_type'] === 3) {

                $this->db->join('left', 'shop_coupon_shipping_link', 'shop_coupon_shipping_link')
                    ->on('shop_coupon_shipping_link.company', '=', 'shop_shipping_option.company')
                    ->andOn('shop_coupon_shipping_link.code', '=', $coupon['code'], true)
                    ->andOn('shop_coupon_shipping_link.coupon_code', '=', $coupon['coupon_code'], true);
            }

            $this->db->where('shop_shipping_option.company','=',$company)
            ->andWhere('shop_shipping_option.shipping_group_code','=',(string)$shippingGroupCode);



        if(count($allowedshippingOptionsIds) > 0) {
            $this->db
                ->enterParentheses('AND');
            $i = 0;
            foreach($allowedshippingOptionsIds as $shippingOption) {
                if($i === 0) {
                    $this->db->where('id', '=',$shippingOption->id);
                } else {
                    $this->db->orWhere('id','=',$shippingOption->id);
                }
                ++$i;
            }

        }
        $this->db->enterParentheses('AND')
                ->where('shop_shipping_option.weight_from','<=',$weight)
                ->orWhere('shop_shipping_option.weight_from','IS NULL')
                ->orWhere('shop_shipping_option.weight_from','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('shop_shipping_option.weight_to','>=',$weight)
                ->orWhere('shop_shipping_option.weight_to','IS NULL')
                ->orWhere('shop_shipping_option.weight_to','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('shop_shipping_option.amount_from','<=',$amount)
                ->orWhere('shop_shipping_option.amount_from','IS NULL')
                ->orWhere('shop_shipping_option.amount_from','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('shop_shipping_option.amount_to','>=',$amount)
                ->orWhere('shop_shipping_option.amount_to','IS NULL')
                ->orWhere('shop_shipping_option.amount_to','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('shop_shipping_option.valid_from','<=',$date)
                ->orWhere('shop_shipping_option.valid_from','IS NULL')
                ->orWhere('shop_shipping_option.valid_from','=',0)
            ->leaveParentheses()
            ->enterParentheses('AND')
                ->where('shop_shipping_option.valid_to','>=',$date)
                ->orWhere('shop_shipping_option.valid_to','IS NULL')
                ->orWhere('shop_shipping_option.valid_to','=',0)
            ->leaveParentheses()
                ->enterParentheses('AND');

                if ($basket->getShippingClassFilter() === 'DEFAULT') {
                    $this->db->where('shop_shipping_option.shipping_class_code', 'IS NULL')
                    ->orWhere('shop_shipping_option.shipping_class_code', '=', '');
                } else {
                    $this->db->where('shop_shipping_option.shipping_class_code','=',$basket->getShippingClassFilter());
                }

        if(count($this->activeCampaignCodes) > 0) {
            $this->db
                ->enterParentheses('AND');
            $i = 0;
            foreach($this->activeCampaignCodes as $campaignCode) {
                if($i === 0) {
                    $this->db->where('shipping_option_link.campaign_code', '=',$campaignCode);
                } else {
                    $this->db->orWhere('shipping_option_link.campaign_code','=',$campaignCode);
                }
                ++$i;
            }
            $this->db
                ->orWhere('shipping_option_link.campaign_code','IS NULL')
                ->orWhere('shipping_option_link.campaign_code','=','')
                ->leaveParentheses();
        } else {
            $this->db
                ->enterParentheses('AND')
                    ->where('shipping_option_link.campaign_code','=','')
                    ->orWhere('shipping_option_link.campaign_code','IS NULL')
                ->leaveParentheses();
        }

        if ($shippingZoneFilterString != '') {
            $this->db
                ->enterParentheses('AND')
                    ->where('shop_shipping_option.shipping_zone_code','IN',$shippingZoneFilterString,false)
                ->leaveParentheses();
        } else {
            $this->db
                ->enterParentheses('AND')
                    ->where('shop_shipping_option.shipping_zone_code','=','')
                    ->orWhere('shop_shipping_option.shipping_zone_code','IS NULL')
                ->leaveParentheses();
        }

        if (null !== $coupon && (int)$coupon['value_type'] === 3) {
            $this->db
                ->enterParentheses('AND')
                    ->where('shipping_option_link.coupon_code','=',$coupon['coupon_code'])
                    ->orWhere('shipping_option_link.coupon_code','=','')
                    ->orWhere('shipping_option_link.coupon_code','IS NULL')
                ->leaveParentheses();
        } else {
            $this->db
                ->enterParentheses('AND')
                 ->where('shipping_option_link.coupon_code','=','')
                 ->orWhere('shipping_option_link.coupon_code','IS NULL')
                ->leaveParentheses();
        }


        $this->db
            ->orderBy($sorting,'ASC')
            ->setConstructedQuery();


        $this->db->doQuery();


        $resultArr = $this->db->getResultArray();
        $resultCount = count($resultArr);

        if($resultCount > 0) {
            $config = new ShippingOptionConfig();
            foreach($resultArr as $shipOpt) {
                $shippingOption = new ShippingOption($config);
                $shippingOption->mapFromArray($shipOpt);
                $collection->add($shippingOption);
            }
        }
        return $collection;
    }


    public function getDescriptionForLanguageByLanguageCodeAndPrimary($languageCode, $primary): string
    {

        /** @var ShippingOption $shippingOption */
        $shippingOption = $this->findByAltPrimary($primary);

        $shippingOptionTranslationPrimary = [
            'company'               =>  $primary['company'],
            'shipping_group_code'   =>  $primary['shipping_group_code'],
            'line_no'               =>  $primary['line_no'],
            'language_code'         =>  $languageCode
        ];

        $translation = $this->shippingOptionTranslationRepository->findByAltPrimary($shippingOptionTranslationPrimary);

        if (null === $translation->id) {
            return (string)$shippingOption->description;
        }
        return (string)$translation->description;
    }

    public function getShippingCostByPrimary($primary): float
    {
        /** @var ShippingOption $shippingOption */
        $shippingOption = $this->findByAltPrimary($primary);
        return $shippingOption->shipping_cost;
    }

    public function getShippingCostByPrimaryAndCoupon($primary,$coupon): float
    {
        /** @var ShippingOption $shippingOption */
        $shippingOption = $this->findByAltPrimary($primary);

        return $shippingOption->shipping_cost;
    }

    /**
     * @param CurrShopConfiguration $configuration
     * @return $ids
     */
    function getCustomerAllowedShippingOptions(CurrShopConfiguration $configuration)
    {
        $company = $configuration->getCompany();
        $shopCode = $configuration->getShopCode();
        $shippingGroupCode = $configuration->getShop()->getShippingGroupCode();
        $customerNo = $configuration->getCustomerNo();
        //TODO query optimierung
        $query = "
        SELECT 
            id
        FROM
            (SELECT DISTINCT
                shop_shipping_option.id
            FROM
                shop_permissions_group_link
            JOIN shop_shipping_option ON shop_permissions_group_link.line_no = shop_shipping_option.line_no
                AND shop_permissions_group_link.shop_code = '".$shopCode."'
                AND shop_permissions_group_link.company = shop_shipping_option.company
            WHERE
                shop_permissions_group_link.type = 6
                    AND shop_permissions_group_link.customer_no = '" . $customerNo . "'
                    AND shop_permissions_group_link.shop_code = '".$shopCode."'
                    AND shop_permissions_group_link.company = '".$company."'
                    AND shop_shipping_option.shipping_group_code = '".$shippingGroupCode."' UNION SELECT DISTINCT
                shop_shipping_option.id
            FROM
                shop_permissions_group_link
            JOIN shop_shipping_option ON shop_permissions_group_link.line_no = shop_shipping_option.line_no
                AND shop_permissions_group_link.shop_code = '".$shopCode."'
                AND shop_permissions_group_link.company = shop_shipping_option.company
            WHERE
                shop_permissions_group_link.type = 5
                    AND shop_permissions_group_link.shop_code = '".$shopCode."'
                    AND shop_permissions_group_link.company = '".$company."'
                    AND shop_permissions_group_link.permission_group_code IN (SELECT 
                        permission_group_code
                    FROM
                        shop_permissions_group_link
                    WHERE
                        customer_no = '" . $customerNo . "'
                            AND company = '".$company."'
                            AND type = 0)
                    AND shop_shipping_option.shipping_group_code = '".$shippingGroupCode."' UNION SELECT DISTINCT
                shop_shipping_option.id
            FROM
                shop_permissions_group_link
            JOIN shop_shipping_option ON shop_permissions_group_link.line_no <> shop_shipping_option.line_no
                AND shop_permissions_group_link.shop_code = '".$shopCode."'
                AND shop_permissions_group_link.company = shop_shipping_option.company
            WHERE
                shop_permissions_group_link.shop_code = '".$shopCode."'
                    AND shop_permissions_group_link.company = '".$company."'
                    AND (shop_permissions_group_link.type = 5
                    OR shop_permissions_group_link.type = 6
                    AND shop_shipping_option.shipping_group_code = '".$shippingGroupCode."')) AS shipping_option
        WHERE
            shipping_option.id NOT IN (SELECT DISTINCT
                    shop_shipping_option.id
                FROM
                    shop_permissions_group_link
                        JOIN
                    shop_shipping_option ON shop_permissions_group_link.line_no = shop_shipping_option.line_no
                        AND shop_permissions_group_link.shop_code = '".$shopCode."'
                        AND shop_permissions_group_link.company = shop_shipping_option.company
                WHERE
                    shop_permissions_group_link.type = 6
                        AND shop_permissions_group_link.customer_no <> '" . $customerNo . "'
                        AND shop_permissions_group_link.shop_code = '".$shopCode."'
                        AND shop_permissions_group_link.company = '".$company."'
                        AND shop_shipping_option.shipping_group_code = '".$shippingGroupCode."' UNION SELECT DISTINCT
                    shop_shipping_option.id
                FROM
                    shop_permissions_group_link
                        JOIN
                    shop_shipping_option ON shop_permissions_group_link.line_no = shop_shipping_option.line_no
                        AND shop_permissions_group_link.shop_code = '".$shopCode."'
                        AND shop_permissions_group_link.company = shop_shipping_option.company
                WHERE
                    shop_permissions_group_link.type = 5
                        AND shop_permissions_group_link.shop_code = '".$shopCode."'
                        AND shop_permissions_group_link.company = '".$company."'
                        AND shop_permissions_group_link.permission_group_code NOT IN (SELECT 
                            permission_group_code
                        FROM
                            shop_permissions_group_link
                        WHERE
                            customer_no <> '" . $customerNo . "'
                                AND company = '".$company."'
                                AND type = 0)
                        AND shop_shipping_option.shipping_group_code = '".$shippingGroupCode."') 
      ";

        $allowedShippingOptions = $this->getQueryResultArray($query);
        return $allowedShippingOptions;

    }

    /**
     * @param $countryCode
     * @param $postCode
     * @param $company
     * @return string
     * @throws \Exception
     */
    public function getShippingZoneFilterString($countryCode, $postCode, $company): string
    {
        $shippingZonesCriteria = [
            [
                ['company', '=', $company]
            ]
        ];

        $shippingZoneFilterString = '';
        $shippingZones = $this->shippingZoneRepository->findByCriteria($shippingZonesCriteria);
        /** @var ShippingZone $shippingzone */
        foreach ($shippingZones as $shippingzone) {
            $addToFilter = false;
            $this->shippingZoneRepository->setLinesForDocument($shippingzone);
            /** @var ShippingZoneLine $shippingZoneLine */
            foreach ($shippingzone->getLines() as $shippingZoneLine) {
                //include
                if ($shippingZoneLine->type === 0) {
                    if ($shippingZoneLine->country_region_code == $countryCode) {
                        if ($shippingZoneLine->post_code_code == '') {
                            $addToFilter = true;
                        } else {
                            if (substr_count($shippingZoneLine->post_code_code, '*') > 0) {
                                $starString = '*';
                            } else {
                                $starString = '';
                            }
                            $postCodeAr = str_split($postCode);
                            $postCodeLen = strlen($postCode);
                            $i = 0;
                            foreach ($postCodeAr as $char) {
                                if ($shippingZoneLine->post_code_code == (substr($postCode, 0, $postCodeLen - $i) . $starString)) {
                                    $addToFilter = true;
                                }
                                $i++;
                            }
                        }
                    }
                }
            }

            foreach ($shippingzone->getLines() as $shippingZoneLine) {
                //exclude
                if ($shippingZoneLine->type === 1) {
                    if ($shippingZoneLine->country_region_code == $countryCode) {
                        if ($shippingZoneLine->post_code_code == '') {
                            $addToFilter = false;
                        } else {
                            if (substr_count($shippingZoneLine->post_code_code, '*') > 0) {
                                $starString = '*';
                            } else {
                                $starString = '';
                            }
                            $postCodeAr = str_split($postCode);
                            $postCodeLen = strlen($postCode);
                            $i = 0;
                            foreach ($postCodeAr as $char) {
                                if ($shippingZoneLine->post_code_code == (substr($postCode, 0, $postCodeLen - $i) . $starString)) {
                                    $addToFilter = false;
                                }
                                $i++;
                            }
                        }
                    } else {
                        if ($shippingZoneLine->post_code_code == '') {
                            $addToFilter = true;
                        } else {
                            if (substr_count($shippingZoneLine->post_code_code, '*') > 0) {
                                $starString = '*';
                            } else {
                                $starString = '';
                            }
                            $postCodeAr = str_split($postCode);
                            $postCodeLen = strlen($postCode);
                            $i = 0;
                            foreach ($postCodeAr as $char) {
                                if ($shippingZoneLine->post_code_code == (substr($postCode, 0, $postCodeLen - $i) . $starString)) {
                                    $addToFilter = true;
                                }
                                $i++;
                            }
                        }
                    }
                }
            }

            if ($addToFilter) {
                if ($shippingZoneFilterString == '') {
                    $shippingZoneFilterString = '("' . $shippingzone->code . '"';
                } else {
                    $shippingZoneFilterString .= ',"' . $shippingzone->code . '"';
                }
            }

        }

        if ($shippingZoneFilterString != '') {
            $shippingZoneFilterString .= ')';
        }
        return $shippingZoneFilterString;
    }

}