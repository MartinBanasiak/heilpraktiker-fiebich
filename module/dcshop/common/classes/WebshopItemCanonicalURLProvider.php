<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\Language;
use DynCom\dc\common\classes\LanguageRepository;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\Site;
use DynCom\dc\common\classes\SiteRepository;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 17.10.2016
 * Time: 02:41
 */
class WebshopItemCanonicalURLProvider
{

    const QUERY_CANONICAL_CATEGORY_URL = '
        SELECT a.url AS \'canonical_url\'
        FROM (
            SELECT c.url AS \'url\' FROM shop_item_has_category ic
            INNER JOIN shop_category c ON (
                  c.company=ic.company
              AND c.shop_code=ic.category_shop_code
              AND c.language_code=ic.category_language_code
              AND c.line_no=ic.category_line_no
            )
            WHERE 
                  ic.company = :company
              AND ic.shop_code = :item_shop_code
              AND ic.category_shop_code = :category_shop_code
              AND ic.language_code = :item_language_code
              AND (ic.item_no = :item_no OR ic.item_no = :parent_item_no)
            ORDER BY 
              CASE WHEN ic.category_line_no = :main_category_line_no THEN 1 ELSE 0 END,
              CASE WHEN ic.category_line_no = :parent_main_category_line_no THEN 1 ELSE 0 END,
              ic.id ASC
            LIMIT 1
        ) AS \'a\'
    ';

    /**
     * @var SiteRepository
     */
    private $siteRepository;
    /**
     * @var WebshopItemRepository
     */
    private $itemRepository;
    /**
     * @var PDOQueryWrapper
     */
    private $db;

    private $sitesByCode = [];

    private $siteLanguageByKey = [];

    private $targetShopByCode = [];

    private $webshopItemByPrimary = [];

    private $parentItemByPrimary = [];

    private $webshopItemCanonicalsByKey = [];

    private $shopByKey = [];
    /**
     * @var WebshopItemVariantService
     */
    private $variantService;
    /**
     * @var ShopRepository
     */
    private $shopRepository;
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * WebshopItemCanonicalURLProvider constructor.
     * @param SiteRepository $siteRepository
     * @param LanguageRepository $languageRepository
     * @param ShopRepository $shopRepository
     * @param WebshopItemRepository $itemRepository
     * @param WebshopItemVariantService $variantService
     * @param PDOQueryWrapper $db
     */
    public function __construct(
        SiteRepository $siteRepository,
        LanguageRepository $languageRepository,
        ShopRepository $shopRepository,
        WebshopItemRepository $itemRepository,
        WebshopItemVariantService $variantService,
        PDOQueryWrapper $db
    ) {
        $this->siteRepository = $siteRepository;
        $this->itemRepository = $itemRepository;
        $this->variantService = $variantService;
        $this->db = $db;
        $this->shopRepository = $shopRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param $string
     * @return string
     */
    public function URLEncodeReadable($string)
    {
        $wordPattern = '/[\p{L}\p{N}]+/u';
        $matches = [];
        $newString = '';
        preg_match_all($wordPattern, $string, $matches);
        foreach ($matches[0] as $match) {
            $newString .= $newString ? '-' : '';
            $newString .= $match;
        }
        return $newString;
    }

    /**
     * @param $siteCode
     * @param $languageCode
     * @param $itemNo
     * @param $variantCode
     * @return string
     */
    public function getCanonicalByPrimary($siteCode, $languageCode, $itemNo, $variantCode)
    {
        $delim = '|';
        $key = $siteCode . $delim . $languageCode . $delim . $itemNo . $delim . $variantCode;


        if (array_key_exists(
                $key,
                $this->webshopItemCanonicalsByKey
            ) && !empty($this->webshopItemCanonicalsByKey[$key])
        ) {
            return $this->webshopItemCanonicalsByKey[$key];
        }


        /**
         * @var $site Site
         * @var $language Language
         * @var $targetShop Shop
         */
        $sitePrimary = ['code' => $siteCode];
        $site = array_key_exists(
            $siteCode,
            $this->sitesByCode
        ) ? $this->sitesByCode[$siteCode] : $this->siteRepository->findByAltPrimary($sitePrimary);


        $useSSL = (bool)$site->use_ssl;
        $domain = $site->site_url;

        $languageKey = $site->getID() . $delim . $languageCode;
        $languagePrimary = ['main_site_id' => $site->getID(), 'code' => $languageCode];
        /**
         * @var $language Language
         */
        $language = array_key_exists(
            $languageKey,
            $this->siteLanguageByKey
        ) ? $this->siteLanguageByKey[$languageKey] : $this->languageRepository->findByAltPrimary($languagePrimary);
        $company = $language->company;
        $targetShopCode = $language->shop_code;
        $shopLanguageCode = $language->shop_language_code;

        $targetShopKey = $company . $delim . $targetShopCode;
        $targetShopPrimary = ['company' => $company, 'code' => $targetShopCode];
        $targetShop = array_key_exists(
            $targetShopKey,
            $this->targetShopByCode
        ) ? $this->targetShopByCode[$targetShopKey] : $this->shopRepository->findByAltPrimary($targetShopPrimary);

        $categoryShopCode = $targetShop->getUseCategoriesFromShopCode();
        $itemShopCode = $targetShop->getUseItemsFromShopCode();

        $webshopItemPrimary = ['company' => $company, 'shop_code' => $itemShopCode, 'language_code' => $shopLanguageCode, 'item_no' => $itemNo];
        $webshopItemKey = $company . $delim . $itemShopCode . $delim . $shopLanguageCode . $delim . $itemNo;

        if (array_key_exists(
                $webshopItemKey,
                $this->webshopItemByPrimary
            ) && !empty($this->webshopItemByPrimary[$webshopItemKey])
        ) {
            $item = $this->webshopItemByPrimary[$webshopItemKey];
        } else {
            $item = $this->itemRepository->findByAltPrimary($webshopItemPrimary);
            $this->webshopItemByPrimary[$webshopItemKey] = $item;
        }

        if (array_key_exists(
                $webshopItemKey,
                $this->parentItemByPrimary
            ) && !empty($this->parentItemByPrimary[$webshopItemKey])
        ) {
            $parentItem = $this->parentItemByPrimary[$webshopItemKey];
        } else {
            $parentItem = $this->variantService->getFirstVariant($item);
            $this->parentItemByPrimary[$webshopItemKey] = $parentItem;
        }

        $itemNo = $item->getItemNo();
        $parentItemNo = $parentItem->getItemNo();

        $mainCategoryLineNo = $item->main_category_line_no;
        $parentMainCategoryLineNo = $parentItem->main_category_line_no;

        $protocol = $useSSL ? 'https' : 'http';
        $canonicalURL = $protocol . '://' . rtrim($domain, '/') . '/' . $siteCode . '/' . $languageCode . '/shop';

        $canonicalCategorySnippet = '';
        $this->db->setQuery(self::QUERY_CANONICAL_CATEGORY_URL);
        $this->db->prepareQuery();
        $params = [
            [':company', $company, \PDO::PARAM_STR],
            [':item_shop_code', $itemShopCode, \PDO::PARAM_STR],
            [':category_shop_code', $categoryShopCode, \PDO::PARAM_STR],
            [':item_language_code', $shopLanguageCode, \PDO::PARAM_STR],
            [':item_no', $itemNo, \PDO::PARAM_STR],
            [':parent_item_no', $parentItemNo, \PDO::PARAM_STR],
            [':main_category_line_no', $mainCategoryLineNo, \PDO::PARAM_INT],
            [':parent_main_category_line_no', $parentMainCategoryLineNo, \PDO::PARAM_INT],
        ];
        $this->db->bindParameters($params);
        $this->db->executePreparedStatement();
        $resArr = $this->db->getResultArray();
        if (isset($resArr[0]['url'])) {
            $canonicalCategorySnippet = '/' . trim($resArr[0]['canonical_url'], '/');
        }
        $canonicalURL .= $canonicalCategorySnippet;
        $canonicalURL .= '/' . $this->URLEncodeReadable($item->getDescription) . '-p' . $item->getID().'/';
        $this->webshopItemCanonicalsByKey[$key] = $canonicalURL;
        return $canonicalURL;
    }

    /**
     * @param $company
     * @param $code
     * @return mixed
     */
    protected function getShop($company, $code)
    {
        $key = $company . '|' . $code;
        if (array_key_exists($key, $this->shopByKey)) {
            return $this->shopByKey[$key];
        }
        $primary = ['company' => $company, 'code' => $code];
        return $this->shopRepository->findByAltPrimary($primary);
    }


}