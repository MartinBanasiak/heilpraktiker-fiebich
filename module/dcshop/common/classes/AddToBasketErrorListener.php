<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\interfaces\Observer;
use DynCom\dc\common\interfaces\SessionFlashMessageBag;
use DynCom\dc\dcShop\interfaces\TextProviderInterface;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 13.09.2016
 * Time: 13:51
 */
class AddToBasketErrorListener implements Observer
{

    public const EVENT_NAME = 'basketAddError';
    public const TC_ITEM = 'item';

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
        if (strpos($eventName,self::EVENT_NAME) !== false) {
            $errorTextCode = $data['error_text_code'];
            $itemNo = $data['item_no'];

            $msgType = SessionFlashMessageBag::TYPE_NOTICE;
            $msgPrefix = $this->textProvider->getText(self::TC_ITEM) . ' ' . $itemNo . ': ';
            $msg = $this->textProvider->getText($errorTextCode);
            if ($msg) {
                $msg = $msgPrefix . $msg;
                $this->sessionFlashMessageBag->set($msgType,$msg);
            }
            /*
            switch ($errorTextCode) {
                case 'basket_error_item_not_active':
                    break;
                case 'basket_error_item_not_available':
                    break;
                case 'basket_error_user_not_logged_in':
                    break;
                case 'basket_error_user_no_order_permission':
                    break;
                case 'basket_error_no_item_permission':
                    break;
                case 'basket_error_item_not_listed':
                    break;
                default: break;
            }*/

        }

    }

}