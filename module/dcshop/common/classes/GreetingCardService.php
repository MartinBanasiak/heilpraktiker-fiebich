<?php
namespace DynCom\dc\dcShop\classes;
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.04.2016
 * Time: 17:17
 */
class GreetingCardService
{
    const POSTFIELD = 'input_message';

    /**
     * @return string
     */
    public function handleGreetingCardRequestAndGetText()
    {
        $greetingCardDataFromRequest = $this->getGreetingCardDataFromRequest();
        return $greetingCardDataFromRequest;
    }


    /**
     * @return string
     */
    public function getGreetingCardDataFromRequest()
    {
        $greetingCardData = '';
		$this->populateGreetingCardData($greetingCardData,$_POST);
        return $greetingCardData;
    }

    /**
     * @param $data
     * @param array $fromArray
     */
    public function populateGreetingCardData(&$data, array &$fromArray) {
		
		if (array_key_exists(self::POSTFIELD,$fromArray)) {
			$value = $fromArray[self::POSTFIELD];
			$greetingCardValue = filter_var($value, FILTER_SANITIZE_STRING);
			$data = $greetingCardValue;            
        }
	}
}