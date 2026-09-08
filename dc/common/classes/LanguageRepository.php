<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\ShopLanguageRepository;
use DynCom\dc\dcShop\classes\ShopRepository;
use DynCom\dc\dcShop\classes\ShopSetupRepository;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 19.01.2015
 * Time: 00:01
 */

class LanguageRepository implements Repository {

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface                     $db
     * @param LanguageConfig             $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param LanguageCollection     $collection
     * @param                                                     $cacheAll
     */
    public function __construct( GenericDBQueryWrapperInterface $db, LanguageConfig $config, CriteriaHelperInterface $criteriaValidationService, LanguageCollection $collection, $cacheAll=false) {
        $this->db                        = $db;
        $this->config                    = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection                = $collection;
        $this->collectionEntryClassName  = $this->collection->getEntryClassName();
    }

    /**
     * @param SiteRepository $siteRepository
     * @return mixed
     */
    public function getFromRequest(SiteRepository $siteRepository) {
        $site = $siteRepository->getFromRequest();
        $main_site_id = $site->getID();
        $std_main_language_id = $site->std_main_language_id;
        if(isset($_GET['language'])) {
            $found = $this->findByAltPrimary(array('main_site_id' => $main_site_id,'code' => filter_var($_GET['language'],FILTER_SANITIZE_SPECIAL_CHARS)));
        } else {
            $found = $this->findByID($std_main_language_id);
        }
        return $found;
    }


    /**
     * @param ShopRepository $shopRepository
     * @param SiteRepository $siteRepository
     * @return \DynCom\dc\dcShop\classes\Shop|mixed
     */
    public function getCurrShopFromRequest(ShopRepository $shopRepository, SiteRepository $siteRepository) {
        $currLanguage = $this->getFromRequest($siteRepository);
        $nullObj = $shopRepository->getNullObject();
        if(!isset($currLanguage->company) || !isset($currLanguage->shop_code)) {
            return $nullObj;
        }
        $altPrimaryArr = [
            'company' => $currLanguage->company,
            'code' => $currLanguage->shop_code
        ];
        $found = $shopRepository->findByAltPrimary($altPrimaryArr);
        return $found;

    }

    /**
     * @param \DynCom\dc\dcShop\classes\ShopSetupRepository $shopSetupRepository
     * @param SiteRepository $siteRepository
     * @return \DynCom\dc\dcShop\classes\Shop|mixed
     */
    public function getCurrShopSetupFromRequest(ShopSetupRepository $shopSetupRepository, SiteRepository $siteRepository) {
        $currLanguage = $this->getFromRequest($siteRepository);
        $nullObj = $shopSetupRepository->getNullObject();
        if(!isset($currLanguage->company)) {
            return $nullObj;
        }
        $altPrimaryArr = [
            'company' => $currLanguage->company
        ];
        $found = $shopSetupRepository->findByAltPrimary($altPrimaryArr);
        return $found;

    }

    /**
     * @param ShopLanguageRepository $shopLanguageRepository
     * @param SiteRepository $siteRepository
     * @return mixed
     */
    public function getCurrShopLanguageFromRequest(ShopLanguageRepository $shopLanguageRepository, SiteRepository $siteRepository) {
        $currLanguage = $this->getFromRequest($siteRepository);
        $nullObj = $shopLanguageRepository->getNullObject();
        if(!isset($currLanguage->company) || !isset($currLanguage->shop_code) || !isset($currLanguage->shop_language_code)) {
            return $nullObj;
        }
        return $shopLanguageRepository->findByAltPrimary(array('company' => $currLanguage->company,'shop_code' => $currLanguage->shop_code, 'code' => $currLanguage->shop_language_code));
    }

}