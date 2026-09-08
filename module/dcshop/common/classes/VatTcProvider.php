<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.01.2016
 * Time: 14:23
 */
class VatTcProvider
{

    private $db;
    private $currConfig;
    private static $tcArr = [
        'value_',
        'item_vat_and_shipping_notice_plus_ship_',
        'item_vat_and_shipping_notice_incl_ship_'
    ];
    private static $query = <<<'SQL'
SELECT vat_prod_posting_group,vat_percent
FROM shop_vat_posting_setup
WHERE company = :company
AND vat_bus_posting_group = :vat_bus_posting_group
SQL;


    /**
     * VatTcProvider constructor.
     * @param PDOQueryWrapper $db
     * @param CurrShopConfiguration $currConfig
     */
    public function __construct(PDOQueryWrapper $db, CurrShopConfiguration $currConfig)
    {
        $this->db = $db;
        $this->currConfig = $currConfig;
    }

    public function setVATTextConstants()
    {
        $params = [
            [':company',$this->currConfig->getCompany(),\PDO::PARAM_STR],
            [':vat_bus_posting_group',$this->currConfig->getShopVATBusPostingGroup(),\PDO::PARAM_STR]
        ];
        $this->db->setQuery(self::$query);
        $this->db->prepareQuery();
        $this->db->bindParameters($params);
        $this->db->executePreparedStatement();
        $arr = $this->db->getResultArray();
        foreach ($arr as $row) {
            $percent = (int)$row['vat_percent'];
            $groupName = $row['vat_prod_posting_group'];
            $testGroupName = 'VAT' . $percent;
            foreach (self::$tcArr as $tc_part) {
                $testTCName = $tc_part . $testGroupName;
                $newTCName = $tc_part . $groupName;
                if (isset($GLOBALS['tc'][$testTCName]) && !isset($GLOBALS['tc'][$newTCName])) {
                    $GLOBALS['tc'][$newTCName] = $GLOBALS['tc'][$testTCName];
                }
            }
        }
    }

}

/*$text_constant["de"]["value_vat1"] = "Preis inkl. 19% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a>";
$text_constant["en"]["value_vat1"] = "Preis inkl. 19% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a>";
$text_constant["de"]["value_vat2"] = "Preis inkl. 7% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a>";
$text_constant["en"]["value_vat2"] = "Preis inkl. 7% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a>";
$text_constant["de"]["value_vat3"] = "Preis inkl. 0% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a>";
$text_constant["en"]["value_vat3"] = "Preis inkl. 0% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a>";
$text_constant["de"]["item_vat_and_shipping_notice_plus_ship_vat1"] = "<span>Preis inkl. 19% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a></span>";
$text_constant["en"]["item_vat_and_shipping_notice_plus_ship_vat1"] = "<span>Price incl. 19% VAT <a href='/b2c/en/footer_menu/shipping_terms/' target='_blank'>plus Shipping</a></span>";
$text_constant["de"]["item_vat_and_shipping_notice_incl_ship_vat1"] = "<span>Preis inkl. 19% MwSt. inkl. Versand</span>";
$text_constant["en"]["item_vat_and_shipping_notice_plus_ship_vat1"] = "<span>Price incl. 19% VAT incl. Shipping</span>";
$text_constant["de"]["item_vat_and_shipping_notice_plus_ship_vat2"] = "<span>Preis inkl. 7% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a></span>";
$text_constant["en"]["item_vat_and_shipping_notice_plus_ship_vat2"] = "<span>Price incl. 7% VAT <a href='/b2c/en/footer_menu/shipping_terms/' target='_blank'>plus Shipping</a></span>";
$text_constant["de"]["item_vat_and_shipping_notice_incl_ship_vat2"] = "<span>Preis inkl. 7% MwSt. inkl. Versand</span>";
$text_constant["en"]["item_vat_and_shipping_notice_incl_ship_vat2"] = "<span>Price incl. 7% VAT plus Shipping</span>";
$text_constant["de"]["item_vat_and_shipping_notice_plus_ship_vat3"] = "<span>Preis inkl. 0% MwSt. <a href='/b2c/de/footer_menu/shipping_terms/' target='_blank'>zzgl. Versand</a></span>";
$text_constant["en"]["item_vat_and_shipping_notice_plus_ship_vat3"] = "<span>Price incl. 0% VAT <a href='/b2c/en/footer_menu/shipping_terms/' target='_blank'>plus Shipping</a></span>";
$text_constant["de"]["item_vat_and_shipping_notice_incl_ship_vat3"] = "<span>Preis inkl. 0% MwSt. inkl. Versand</span>";
$text_constant["en"]["item_vat_and_shipping_notice_incl_ship_vat3"] = "<span>Price incl. 0% VAT incl. Shipping</span>";*/