<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\GenericDBQueryWrapperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;

/**
 * Class WebshopItemFileRepository
 * @package DynCom\dc\dcShop\classes
 */
class WebshopItemFileRepository implements Repository
{

    use genericRepositoryTrait;

    /**
     * @param GenericDBQueryWrapperInterface $db
     * @param WebshopItemFileConfig $config
     * @param CriteriaHelperInterface $criteriaValidationService
     * @param WebshopItemFileCollection $collection
     * @param $cacheAll
     */
    public function __construct(GenericDBQueryWrapperInterface $db, WebshopItemFileConfig $config, CriteriaHelperInterface $criteriaValidationService, WebshopItemFileCollection $collection, $cacheAll=false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->criteriaValidationService = $criteriaValidationService;
        $this->collection = $collection;
        $this->collectionEntryClassName = $this->collection->getEntryClassName();
        $this->cacheAll = $cacheAll;
    }

    /**
     * @return WebshopItemFile
     */
    public function getNullObject()
    {
        return new WebshopItemFile($this->config);
    }

    /**
     * @return WebshopItemFileCollection
     */
    public function getEmptyCollection()
    {
        return $this->collection->getEmptyCollection();
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemShopLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @param $itemMainImageLineNo
     * @return WebshopItemFileCollection
     * @throws \Exception
     */
    public function getAllImagesForItem($company, $itemShopCode, $itemShopLanguageCode, $itemNo, $variantCode, $itemMainImageLineNo)
    {
        $criteria = [
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['language_code','=',$itemShopLanguageCode],
                ['type','=',WebshopItemFile::TYPE_IMAGE],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ],
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['all_language_codes','=','1'],
                ['type','=',WebshopItemFile::TYPE_IMAGE],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ]
        ];
        $returnCollection = $this->getEmptyCollection();
        $resultCollection = $this->findByCriteria($criteria);
        $mainImage = null;
        /**
         * @var $itemFile WebshopItemFile
         */
        foreach ($resultCollection as $itemFile) {
            if ($itemFile->line_no == $itemMainImageLineNo && $itemMainImageLineNo > 0) {
                $mainImage = $itemFile;
                $resultCollection->remove($itemFile,false);
            }
        }
        if (!isset($mainImage)) {
            $mainImage = $resultCollection->getFirst();
            $resultCollection->remove($mainImage,false);
        }
        $returnCollection->add($mainImage);
        foreach ($resultCollection as $itemFile)
        {
            $returnCollection->add($itemFile);
        }
        return $returnCollection;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemShopLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     * @throws \Exception
     */
    public function getAllDocumentsForItem($company, $itemShopCode, $itemShopLanguageCode, $itemNo, $variantCode)
    {
        $criteria = [
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['language_code','=',$itemShopLanguageCode],
                ['type','=',WebshopItemFile::TYPE_DOCUMENT_FILE],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ],
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['all_language_codes','=',1],
                ['type','=',WebshopItemFile::TYPE_DOCUMENT_FILE],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ]
        ];

        $returnCollection = $this->findByCriteria($criteria);

        return $returnCollection;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemShopLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     * @throws \Exception
     */
    public function getAllVideosForItem($company, $itemShopCode, $itemShopLanguageCode, $itemNo, $variantCode)
    {
        $criteria = [
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['language_code','=',$itemShopLanguageCode],
                ['type','=',2],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ],
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['all_language_codes','=',2],
                ['type','=',1],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ]
        ];

        $returnCollection = $this->findByCriteria($criteria);

        return $returnCollection;
    }

    /**
     * @param $company
     * @param $itemShopCode
     * @param $itemShopLanguageCode
     * @param $itemNo
     * @param $variantCode
     * @return \DynCom\dc\common\interfaces\GenericCollectionInterface
     * @throws \Exception
     */
    public function getAll360DegreeImagesForItem($company, $itemShopCode, $itemShopLanguageCode, $itemNo, $variantCode)
    {
        $criteria = [
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['language_code','=',$itemShopLanguageCode],
                ['type','=',WebshopItemFile::TYPE_360_DEGREE_IMAGE],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ],
            [
                ['company','=',$company],
                ['shop_code','=',$itemShopCode],
                ['all_language_codes','=',2],
                ['type','=',WebshopItemFile::TYPE_360_DEGREE_IMAGE],
                ['variant_code','=',(string)$variantCode],
                ['item_no','=',$itemNo],
            ]
        ];

        $returnCollection = $this->findByCriteria($criteria);

        return $returnCollection;
    }

}