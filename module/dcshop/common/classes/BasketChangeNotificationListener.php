<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 02.02.2017
 * Time: 13:08
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\common\interfaces\Observer;
use DynCom\dc\common\interfaces\SessionFlashMessageBag;
use DynCom\dc\dcShop\interfaces\TextProviderInterface;

class BasketChangeNotificationListener implements Observer
{

    public const EVENT_NAME_ITEM_ADDED = 'afterSuccessfullAddToBasket';
    public const EVENT_NAME_ITEM_REMOVED = 'afterSuccessfullRemoveFromBasket';
    public const EVENT_NAME_ITEM_QTY_CHANGED = 'afterSuccessfullyBasketQtyChange';
    public const EVENT_NAME_ITEM_QTY_ADJUSTED = 'basketQtyAdujsted';

    protected const EVENT_NAMES = [
        self::EVENT_NAME_ITEM_ADDED,
        self::EVENT_NAME_ITEM_REMOVED,
        self::EVENT_NAME_ITEM_QTY_CHANGED,
        self::EVENT_NAME_ITEM_QTY_ADJUSTED,
    ];
    protected const TC_ITEM = 'item';

    private $sessionFlashMessageBag;
    private $textProvider;

    /**
     * AddToBasketErrorListener constructor.
     * @param SessionFlashMessageBag $msgBag
     * @param TextProviderInterface $textProvider
     */
    public function __construct(SessionFlashMessageBag $msgBag, TextProviderInterface $textProvider)
    {
        $this->sessionFlashMessageBag = $msgBag;
        $this->textProvider = $textProvider;
    }

    /**
     * @param $eventName
     * @param $data
     */
    public function notify($eventName, $data)
    {
        $itemNo = $data['item_no'];
        $varCode = $data['var_code'] ?? '';
        $qty = $data['new_qty'] ?? 0.00;
        $oldQty = $data['old_qty'] ?? 0.00;
        $reqQty = $data['requested_quantity'] ?? 0.00;
        $shopType = (int)$data['shop_type'];

        if ($varCode) {
            $varCode = ' - ' . $varCode;
        }

        $toReplace = [
            '{%item_no%}',
            '{%var_code%}',
            '{%qty%}',
            '{%new_qty%}',
            '{%old_qty%}',
            '{%req_qty%}',
        ];


        $replaceWith = [
            $itemNo,
            $varCode,
            $qty,
            $qty,
            $oldQty,
            $reqQty,
        ];

        if (\in_array($eventName,self::EVENT_NAMES,true)) {
            switch ($eventName) {
                case self::EVENT_NAME_ITEM_ADDED:
                    if ($shopType !== 1) {
                        $msgType = SessionFlashMessageBag::TYPE_SUCCESS;
                        $this->setMsg($msgType,'item_added_to_basket_notification',$toReplace,$replaceWith);
                    }
                    break;
                case self::EVENT_NAME_ITEM_QTY_CHANGED:
                    if ($shopType !== 1 && $oldQty > 0) {
                        $msgType = SessionFlashMessageBag::TYPE_SUCCESS;
                        $this->setMsg($msgType,'item_qty_changed_in_basket_notification',$toReplace,$replaceWith);
                    }
                    break;
                case self::EVENT_NAME_ITEM_REMOVED:
                    $msgType = SessionFlashMessageBag::TYPE_SUCCESS;
                    $this->setMsg($msgType,'item_removed_from_basket_notification',$toReplace,$replaceWith);
                    break;
                case self::EVENT_NAME_ITEM_QTY_ADJUSTED:
                    if ($oldQty > 0) {
                        $msgType = SessionFlashMessageBag::TYPE_NOTICE;
                        $this->setMsg($msgType, 'item_qty_adjusted_in_basket', $toReplace, $replaceWith);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    protected function setMsg(string $type, string $textConstantName,array $toReplace = [], array $replaceWith = []) : void
    {
        $rawMsg = $this->textProvider->getText($textConstantName);
        $processedMsg = str_replace($toReplace,$replaceWith,$rawMsg);
        $this->sessionFlashMessageBag->set($type,$processedMsg);
    }



}