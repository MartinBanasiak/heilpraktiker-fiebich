<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\interfaces\ItemAvailabilityProvider;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\interfaces\WebshopItemWithCategories;
use DynCom\dc\dcShop\interfaces\WebshopItemWithImages;
use DynCom\dc\dcShop\interfaces\WebshopItemWithVariants;
use DynCom\dc\dcShop\ShippingOptions\ShippingClassPriorityProvider;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 21.10.2016
 * Time: 13:39
 */
class WebshopItemFullAggregate extends WebshopItemOrderableEntityDecorator implements WebshopItemInterface, WebshopItemWithCategories, WebshopItemWithImages, WebshopItemWithVariants
{

    /**
     * @var WebshopItemFileCollection
     */
    protected $images;

    /**
     * @var WebshopItemFileCollection
     */
    protected $documents;


    /**
     * @var WebshopItemFileCollection
     */
    protected $videos;

    /**
     * @var CategoryCollection
     */
    protected $categories;

    /**
     * @var string
     */
    protected $canonicalURL;

    /**
     * @var WebshopItemDescriptionCollection
     */
    protected $headDescriptions;

    /**
     * @var WebshopItemDescriptionCollection
     */
    protected $bodyDescriptions;

    /**
     * @var WebshopItemDescriptionCollection
     */
    protected $attributes;

    protected $reviews = [];

    /**
     * @var WebshopItemInterface
     */
    protected $parentItem;
    /**
     * @var WebshopItemCollection
     */
    private $variants;
    /**
     * @var ShippingClassPriorityProvider
     */
    private $shippingClassPriorityProvider;

    /**
     * WebshopItemFullAggregate constructor.
     * @param WebshopItemInterface $item
     * @param WebshopItemInterface $parentItem
     * @param WebshopItemCollection $variants
     * @param Customer $customer
     * @param AdvancedPriceProvider $priceProvider
     * @param ItemAvailabilityProvider $availabilityProvider
     * @param ShippingClassPriorityProvider $shippingClassPriorityProvider
     * @param $currencyCode
     * @param WebshopItemFileCollection $images
     * @param WebshopItemFileCollection $documents
     * @param WebshopItemFileCollection $videos
     * @param WebshopItemCategoryCollection $categories
     * @param $canonicalURL
     * @param WebshopItemDescriptionCollection $headDescriptions
     * @param WebshopItemDescriptionCollection $bodyDescriptions
     * @param WebshopItemAttributeCollection $attributeCollection
     * @param array $reviews
     */
    public function __construct(
        WebshopItemInterface $item,
        WebshopItemInterface $parentItem,
        WebshopItemCollection $variants,
        Customer $customer,
        AdvancedPriceProvider $priceProvider,
        ItemAvailabilityProvider $availabilityProvider,
        ShippingClassPriorityProvider $shippingClassPriorityProvider,
        $currencyCode,
        WebshopItemFileCollection $images,
        WebshopItemFileCollection $documents,
        WebshopItemFileCollection $videos,
        WebshopItemCategoryCollection $categories,
        $canonicalURL,
        WebshopItemDescriptionCollection $headDescriptions,
        WebshopItemDescriptionCollection $bodyDescriptions,
        WebshopItemAttributeCollection $attributeCollection,
        array $reviews

    ) {
        parent::__construct($item, $customer, $priceProvider, $availabilityProvider, $shippingClassPriorityProvider, $currencyCode);
        $this->images = $images;
        $this->documents = $documents;
        $this->videos = $videos;
        $this->categories = $categories;
        $this->canonicalURL = $canonicalURL;
        $this->headDescriptions = $headDescriptions;
        $this->bodyDescriptions = $bodyDescriptions;
        $this->attributes = $attributeCollection;
        $this->reviews = $reviews;
        $this->parentItem = $parentItem;
        $this->variants = $variants;
        $this->shippingClassPriorityProvider = $shippingClassPriorityProvider;
    }

    /**
     * @param $categoryCompany
     * @param $categoryShopCode
     * @param $categoryShopLanguageCode
     * @param $categoryLineNo
     * @return bool
     */
    public function isInCategoryPrimary($categoryCompany, $categoryShopCode, $categoryShopLanguageCode, $categoryLineNo)
    {
        foreach ($this->categories as $category) {
            /**
             * @var $category Category
             */
            if (
                $category->company === $categoryCompany
                && $category->shop_code === $categoryShopCode
                && $category->language_code === $categoryShopLanguageCode
                && (int)$category->line_no === (int)$categoryLineNo
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $categoryLineNo
     * @return bool
     */
    public function isInCategory($categoryLineNo)
    {
        foreach ($this->categories as $category) {
            /**
             * @var $category Category
             */
            if ((int)$category->line_no === (int)$categoryLineNo) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getCategoryArr()
    {
        $arr = [];
        foreach ($this->categories as $category) {
            /**
             * @var $category Category
             */
            $arr[] = $category->getAllFieldsAsArray();
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getImageData()
    {
        /**
         * @var $image WebshopItemFile
         */
        $arr = [];
        foreach ($this->images as $image) {
            $arr[] = $image->getAllFieldsAsArray();
        }
        return $arr;
    }

    /**
     * @return mixed|object
     */
    public function getMainImageData()
    {
        /**
         * @var $image WebshopItemFile
         */
        $mainImage = $this->images->getFirst();
        $mainImage = $mainImage->getAllFieldsAsArray();
        return $mainImage;
    }

    /**
     * @return WebshopItemCollection
     */
    public function getAllVariants()
    {
        return $this->variants;
    }

    /**
     * @param $identifier
     */
    public function getVariantByIdentifier($identifier)
    {
        // TODO: Implement getVariantByIdentifier() method.
    }

}