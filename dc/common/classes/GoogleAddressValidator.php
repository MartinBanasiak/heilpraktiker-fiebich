<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.06.2017
 * Time: 14:08
 */

namespace DynCom\dc\common\classes;

use DynCom\dc\common\interfaces\AddressValidator;
use GuzzleHttp\Client;

class GoogleAddressValidator implements AddressValidator
{
    protected const GOOGLE_GEOCODE_JSON_ENDPOINT_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    protected function initialize()
    {

        $this->apiKey = getenv('GOOGLE_MAPS_API_KEY') ?? '';
        $client = new Client(
            ['headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ]]
        );
        $this->client = $client;
        $this->isInitialized = true;
    }

    public function isAddressValid(string $address): bool
    {
        if (!$this->isInitialized) {
            $this->initialize();
        }

        $endpoint = self::GOOGLE_GEOCODE_JSON_ENDPOINT_URL . '?address=' . urlencode($address);
        $response = $this->client->get($endpoint);
        if ($response->getStatusCode() != '200') {
            return false;
        }

        $responseBodyContent = $response->getBody()->getContents();
        try {
            $decodedBody = \GuzzleHttp\json_decode($responseBodyContent, true);
            if (array_key_exists('status',$decodedBody) && $decodedBody['status'] === 'OK') {
                return true;
            }
        } catch (\Throwable $t) {
            return false;
        }
        return false;
    }


}