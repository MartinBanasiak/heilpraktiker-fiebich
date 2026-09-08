<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\dcShop\abstracts\WebshopItemDecorator;
use DynCom\dc\dcShop\interfaces\WebshopItemInterface;
use DynCom\dc\dcShop\traits\genericDecoratorTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 17.10.2016
 * Time: 00:20
 */
class WebshopItemFilesDecorator extends WebshopItemDecorator
{
    public const FILE_TYPE_IMAGE = WebshopItemFile::TYPE_IMAGE;
    public const FILE_TYPE_DOWNLOAD = WebshopItemFile::TYPE_DOCUMENT_FILE;
    public const FILE_TYPE_VIDEO = WebshopItemFile::TYPE_VIDEO_FILE;
    public const FILTE_TYPE_YOUTUBE_ID= WebshopItemFile::TYPE_YOUTUBE_ID;

    use genericDecoratorTrait;

    /**
     * @var WebshopItemInterface
     */
    protected $parentItem;
    /**
     * @var WebshopItemVariantService $variantService
     */
    protected $variantService;
    /**
     * @var WebshopItemFileRepository
     */
    protected $itemFileRepository;
    /**
     * @var WebshopItemFileCollection
     */
    protected $images;
    /**
     * @var WebshopItemFileCollection
     */
    protected $downloads;
    /**
     * @var WebshopItemFileCollection
     */
    protected $videos;

    /**
     * @var WebshopItemFileCollection
     */
    protected $youtubeIDs;

    protected $magic360Images;

    /**
     * @var WebshopItemFile|null
     */
    protected $mainImage;

    protected $eagerLoading = false;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;
    /**
     * WebshopItemFilesDecorator constructor.
     * @param WebshopItemInterface $item
     * @param WebshopItemVariantService $variantService
     * @param WebshopItemFileRepository $itemFileRepository
     * @param bool $eagerLoading
     * @throws \Exception
     */
    public function __construct(WebshopItemInterface $item, WebshopItemVariantService $variantService, WebshopItemFileRepository $itemFileRepository, $eagerLoading = false, LoggerInterface $logger = null)
    {
        parent::__construct($item);
        $this->variantService = $variantService;
        $this->parentItem = $variantService->getParentItem($item);
        $this->itemFileRepository = $itemFileRepository;
        $this->images = $itemFileRepository->getEmptyCollection();
        $this->downloads = $itemFileRepository->getEmptyCollection();
        $this->videos = $itemFileRepository->getEmptyCollection();
        $this->youtubeIDs = $itemFileRepository->getEmptyCollection();
        $this->magic360Images = $itemFileRepository->getEmptyCollection();
        if (null === $logger) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
        if ($eagerLoading) {
            $this->eagerLoading = true;
            $this->fetchAllWithFallbacks();
        }

    }

    /**
     * @param $type
     * @param $itemNo
     * @param $variantCode
     * @throws \Exception
     */
    protected function fetchAllByTypeForItem($type, $itemNo, $variantCode)
    {
        $criteria = [
            [
                ['company','=',$this->getCompany()],
                ['shop_code','=',$this->getShopCode()],
                ['language_code','=',$this->getLanguageCode()],
                ['item_no','=',(string)$itemNo],
                ['variant_code','=',(string)$variantCode],
                ['type','=',(int)$type],
            ],
            [
                ['company','=',$this->getCompany()],
                ['shop_code','=',$this->getShopCode()],
                ['all_language_codes','=', 1],
                ['item_no','=',(string)$itemNo],
                ['variant_code','=',(string)$variantCode],
                ['type','=',(int)$type],
            ],
        ];
        $this->logger->error('File criteria: ' . print_r($criteria,1));
        $elements = $this->itemFileRepository->findByCriteria($criteria);
        $collectionRef = $this->getCollectionReferenceByType($type);
        foreach ($elements as $element) {
            $collectionRef->add($element);
        }
    }

    /**
     * @param $type
     * @throws \Exception
     */
    protected function fetchByTypeWithFallbacks($type)
    {
        if (!empty($this->getVariantCode())) {
            $this->fetchAllByTypeForItem($type,$this->getItemNo(),$this->getVariantCode());
            $this->fetchAllByTypeForItem($type,$this->getItemNo(),'');
        } elseif ($this->getItemNo() !== $this->parentItem->getItemNo() && !empty($this->parentItem->getItemNo())) {
            $this->fetchAllByTypeForItem($type,$this->getItemNo(),'');

            $this->fetchAllByTypeForItem($type,$this->parentItem->getItemNo(),'');
        } else {
            $this->fetchAllByTypeForItem($type,$this->getItemNo(),$this->getVariantCode());
        }

        $firstVariant = $this->variantService->getFirstVariant($this->decoratedEntity);
        $collectionRef = $this->getCollectionReferenceByType($type);

        if ($firstVariant->getItemNo() !== $this->getItemNo() || $firstVariant->getVariantCode() !== $this->getVariantCode()) {
            if (!(\count($collectionRef) > 0)) {
                $this->fetchAllByTypeForItem($type,$firstVariant->getItemNo(),$firstVariant->getVariantCode());
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function fetchAllWithFallbacks()
    {
        $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_IMAGE);
        $this->setMainImage();
        $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_DOCUMENT_FILE);
        $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_VIDEO_FILE);
        $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_YOUTUBE_ID);
        $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_360_DEGREE_IMAGE);
    }

    /**
     * @param $type
     * @return WebshopItemFileCollection
     */
    protected function getCollectionReferenceByType($type) :WebshopItemFileCollection
    {
        $type = (int)$type;
        $ref = &$this->images;
        if ($type === WebshopItemFile::TYPE_IMAGE) {
            $ref = &$this->images;
        } elseif ($type === WebshopItemFile::TYPE_DOCUMENT_FILE) {
            $ref = &$this->downloads;
        } elseif ($type === WebshopItemFile::TYPE_VIDEO_FILE) {
            $ref = &$this->videos;
        } elseif ($type === WebshopItemFile::TYPE_YOUTUBE_ID) {
            $ref = &$this->youtubeIDs;
        } elseif ($type === WebshopItemFile::TYPE_360_DEGREE_IMAGE) {
            $ref = &$this->magic360Images;
        }
        return $ref;
    }

    /**
     * @return WebshopItemFileCollection
     * @throws \Exception
     */
    public function getImages() : WebshopItemFileCollection
    {
        if (!$this->eagerLoading && !(count($this->images) > 0)) {
            $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_IMAGE);
            $this->setMainImage();
        }
        return $this->images;
    }

    /**
     * @return WebshopItemFileCollection
     * @throws \Exception
     */
    public function getDownloads() : WebshopItemFileCollection
    {
        if (!$this->eagerLoading && !(count($this->downloads) > 0)) {
            $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_DOCUMENT_FILE);
        }
        return $this->downloads;
    }

    /**
     * @return WebshopItemFileCollection
     * @throws \Exception
     */
    public function getVideos() : WebshopItemFileCollection
    {
        if (!$this->eagerLoading && !(count($this->videos) > 0)) {
            $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_VIDEO_FILE);
        }
        return $this->videos;
    }

    /**
     * @return WebshopItemFileCollection
     * @throws \Exception
     */
    public function getYoutubeIDs() : WebshopItemFileCollection
    {
        if (!$this->eagerLoading && !(count($this->videos) > 0)) {
            $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_YOUTUBE_ID);
        }
        return $this->videos;
    }

    /**
     * @throws \Exception
     */
    protected function setMainImage()
    {
        if (!$this->eagerLoading && !(\count($this->images) > 0)) {
            $this->fetchByTypeWithFallbacks(WebshopItemFile::TYPE_IMAGE);
        }
        $mainImage = $this->images->getFirst();
        $mainImageLineNo = (int)$this->main_picture_line_no;
        if ($mainImageLineNo !== 0) {
            foreach ($this->images as $image) {
                if ((int)$image->line_no === $mainImageLineNo) {
                    $mainImage = $image;
                    break;
                }
            }
        }
        $this->mainImage = $mainImage;
    }

    /**
     * @return WebshopItemFile|null
     * @throws \Exception
     */
    public function getMainImage() : ?WebshopItemFile
    {
        if (null === $this->mainImage) {
            $this->setMainImage();
        }
        return $this->mainImage;
    }

}