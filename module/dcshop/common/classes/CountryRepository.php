<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class CountryRepository
 */
class CountryRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * CountryRepository constructor.
     * @param PDOQueryWrapper $db
     * @param CountryConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param CountryCollection $collection
     * @param $cacheAll
     */
    public function __construct(PDOQueryWrapper $db, CountryConfig $config, CriteriaHelperInterface $criteriaValidationService, CountryCollection $collection, $cacheAll = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return Country
     */
    public function getNullObject()
    {
        return new Country($this->config);
    }

    /**
     * @param ShopLanguage $shopLanguage
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    public function getAllowedShipToCountries(ShopLanguage $shopLanguage)
    {
        $criteria =
            [
                [
                    ['company', '=', $shopLanguage->company],
                    ['shop_code', '=', $shopLanguage->shop_code],
                    ['language_code', '=', $shopLanguage->code],
                    ['ship_to', '=', 1]
                ]
            ];
        return $this->findByCriteria($criteria);
    }

    /**
     * @param ShopLanguage $shopLanguage
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     */
    public function getAllowedBillToCountries(ShopLanguage $shopLanguage)
    {
        $criteria = [
            [
                ['company', '=', $shopLanguage->company],
                ['shop_code', '=', $shopLanguage->shop_code],
                ['language_code', '=', $shopLanguage->code],
                ['invoice_to', '=', 1]
            ]
        ];
        return $this->findByCriteria($criteria);
    }


    public function getCountyByCode($company, $shopCode, $shopLanguageCode, $countryCode)
    {
        $criteria = [
            [
                ['company', '=', $company],
                ['shop_code', '=', $shopCode],
                ['language_code', '=', $shopLanguageCode],
                ['country_code', '=', $countryCode],
            ]
        ];
        return $this->findByCriteria($criteria);
    }


}