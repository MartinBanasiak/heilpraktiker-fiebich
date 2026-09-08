<?php
namespace DynCom\dc\dcShop\campaigns;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\interfaces\CriteriaHelperInterface;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\traits\genericRepositoryTrait;
use DynCom\dc\dcShop\classes\CurrShopConfiguration;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 22.10.2015
 * Time: 13:29
 */
class GenericCampaignRepository extends GenericCampaignHeaderRepository implements Repository
{
    use genericRepositoryTrait;

    private $headerRepository;
    private $elementRepository;


    public function __construct( PDOQueryWrapper $db, GenericCampaignHeaderConfig $config, CriteriaHelperInterface $criteriaValidationService, GenericCampaignHeaderCollection $collection, GenericCampaignHeaderRepository $headerRepository, GenericCampaignElementRepository $elementRepository, $cacheAll = false) {
        parent::__construct($db,$config,$criteriaValidationService,$collection,$cacheAll=false);
        $this->headerRepository = $headerRepository;
        $this->elementRepository = $elementRepository;
    }

    /**
     * @return GenericCampaign
     */
    public function getNullObject() {
        $coll = new GenericCampaignElementCollection(new GenericCampaignElementConfig(),$this->criteriaValidationService);
        return new GenericCampaign($this->config,$coll,$coll);
    }

    protected function _findByAltPrimary(array $searchAltPrimary)
    {
        $header = parent::_findByAltPrimary($searchAltPrimary);
        return $this->decorateWithElements($header);
    }

    protected function _findByCriteria(array $criteriaArr,$offset = 0,$limit = 0) {
        $headerCollection = parent::_findByCriteria($criteriaArr,$offset,$limit);
        $newCollection = new GenericCampaignHeaderCollection($this->config,$this->criteriaValidationService);
        foreach($headerCollection as $header) {
            $header = $this->decorateWithElements($header);
            $newCollection->add($header);
        }
        return $newCollection;
    }

    protected function _findByID($id)
    {
        $header = parent::_findByID($id);
        return $this->decorateWithElements($header);
    }

    private function decorateWithElements(GenericCampaign $header)
    {
        $headerID = (int)$header->getID();
        if($headerID > 0) {
            $conditionCriteria = [
                [
                    ['company','=',$header->getCompany()],
                    ['link_to_header_code','=',$header->getCode()],
                    ['link_type','=',GenericCampaignElement::LINK_TYPE_CONDITION]
                ]
            ];
            $actionCriteria = [
                [
                    ['company','=',$header->getCompany()],
                    ['link_to_header_code','=',$header->getCode()],
                    ['link_type','=',GenericCampaignElement::LINK_TYPE_ACTION]
                ]
            ];
            $conditionElementCollection = $this->elementRepository->findByCriteria($conditionCriteria);
            $actionElementCollection = $this->elementRepository->findByCriteria($actionCriteria);
            $arr = [
                'conditionElements' => $conditionElementCollection,
                'actionElements' => $actionElementCollection
            ];
            $header->mapFromArray($arr);
        }
        return $header;
    }


    public function getAllForCurrShopConfig(CurrShopConfiguration $configuration)
    {
        $campaigns = [];
        if($this->db instanceof PDOQueryWrapper) {
            $this->db
                ->select('*')
                ->from($this->headerRepository->config->getTableName())
                ->where('company','=',$configuration->getCompany())
                ->enterParentheses('AND')
                ->where('shop_code','=',$configuration->getShopCode())
                ->orWhere('all_shops','=',true)
                ->leaveParentheses()
                ->enterParentheses('AND')
                ->where('language_code','=',$configuration->getShopLanguageCode())
                ->orWhere('all_languages','=',true)
                ->leaveParentheses()
                ->orderBy('priority','ASC')
                ->setConstructedQuery()
                ->doQuery();
            if($this->db->getNoOfReturnedRows() > 0) {
                $campaignHeadersArr = $this->db->getResultArray();
                $emptyCollection = new GenericCampaignElementCollection(new GenericCampaignElementConfig(),$this->criteriaValidationService);
                foreach($campaignHeadersArr as $campaignHeaderArr) {
                    $campaignHeader = new GenericCampaign($this->config,$emptyCollection,$emptyCollection);
                    $campaignHeader->mapFromArray($campaignHeaderArr);
                    if($campaignHeader->isCurrentlyActive()) {
                        $campaign = $this->decorateWithElements($campaignHeader);
                        $campaigns[] = $campaign;
                    }
                }
            }
        }
        return $campaigns;
    }

}