<?php
/**
 * Created by PhpStorm.
 * User: lorenz
 * Date: 12.07.2017
 * Time: 13:18
 */

namespace DynCom\dc\common\classes;
use DynCom\dc\common\classes\Site;
use GuzzleHttp;


class CleverReachConnector
{

    private $account;
    private $login;
    private $pass;
    private $groupId;
    private $authToken;
    private $client;
    private $addTokenParam = false;
    private $hasUrlParams = false;
    private $globalAttributes = array();
    private $groupAttributes = array();
    private $error = false;
    private $errorMessages = array();
    private $optInFormId = null;

    const CLEVERREACH_BASE_URL = 'https://rest.cleverreach.com/v2/';



    /**
     * CleverReachConnector constructor.
     * @param $account
     * @param $login
     * @param $pass
     * @param $group
     */
    public function __construct($account, $login, $pass, $group)
    {
        $this->account = $account;
        $this->login = $login;
        $this->pass = $pass;

        if (!isset($this->client) || !($this->client instanceof GuzzleHttp\Client)) {
            $this->client = new GuzzleHttp\Client(['http_errors' => false]);
        }
        $this->login();

        if (is_int($group)) {
            $this->setGroupById($group);
        } elseif (is_string($group)) {
            $this->setGroupByName($group);
        }

        $this->setGlobalAttributes();
        $this->setLocalAttributes();
        $this->getOptInFormId();
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }



    /**
     * @param string $method
     * @param string $operation
     * @param array $data
     */
    private function request(string $method = 'POST', string $operation, array $data = []) {
        $response = null;
        if (isset($this->authToken) || !$this->addTokenParam) {
            $response = $this->client->request($method, '' . self::CLEVERREACH_BASE_URL . $operation . ($this->addTokenParam ? $this->getTokenParam() : ''), ['body' => json_encode($data)]);
        }
        unset($this->addTokenParam);
        unset($this->hasUrlParams);
        return $response;
    }

    private function getResponseStatusCode($response) {
        return $response->getStatusCode();
    }

    private function getResponseBodyContent($response) {
        return json_decode($response->getBody()->getContents());
    }

    private function getTokenParam() {
        return ($this->hasUrlParams ? '&' : '?') . 'token=' . $this->authToken;
    }

    private function setErrorMessage($errorMessage,$response = null) {
        $errorMessage = (!is_null($response) ? $errorMessage . " - Statuscode: " . $this->getResponseStatusCode($response) : $errorMessage);
        return $errorMessage;
    }

    private function login() {
        if (isset($this->login) && isset($this->account) && isset($this->pass)) {
            $response = $this->request('POST','login.json',["client_id" => $this->account,"login" => $this->login,"password" => $this->pass],false);
            if ($this->getResponseStatusCode($response) == 200) {
                $this->authToken = $this->getResponseBodyContent($response);
            } else {
                $this->error = true;
                $this->errorMessages["login"] = $this->setErrorMessage("Login Error",$response);
            }
        }
    }

    public function refresh() {
        $response = $this->request('POST','refresh.json');
        if ($this->getResponseStatusCode($response) == 200) {
            $this->authToken = $this->getResponseBodyContent($response);
        } else {
            $this->error = true;
            $this->errorMessages["refresh"] = $this->setErrorMessage("Login Error",$response);
        }
    }

    public function getOptInFormId() {
        $this->addTokenParam = true;
        $response = $this->request('GET','forms.json');
        if (null !== $response && $response->getStatusCode() == 200) {
            $forms = $this->getResponseBodyContent($response);
            foreach ($forms as $form) {
                if ($form->name === "DOI" && $form->customer_tables_id === $this->getGroupId()) {
                    $this->optInFormId = $form->id;
                    return;
                }
            }
        }
        $this->error = true;
        $this->errorMessages["getOptInFormId"] = $this->setErrorMessage("getOptInFormId Error",$response);
    }

    public function getClients() {
        $this->addTokenParam = true;
        $response = $this->request('GET','clients.json');
        if ($this->getResponseStatusCode($response) == 200) {
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["getClients"] = $this->setErrorMessage("Client Error",$response);
        return false;
    }

    public function createGroup(string $name) {
        $this->addTokenParam = true;
        $response = $this->request('POST','groups.json',["name" => $name]);
        if ($this->getResponseStatusCode($response) == 200) {
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["createGroup"] = $this->setErrorMessage("Create Group Error",$response);
        return false;
    }

    private function getGroups() {
        $this->addTokenParam = true;
        $response = $this->request('GET','groups.json');
        if ($this->getResponseStatusCode($response) == 200) {
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["getGroups"] = $this->setErrorMessage("Get Groups Error",$response);
        return false;
    }

    public function getGroup(int $groupId) {

    }

    private function setGroupById(int $groupId) {
        $this->setGroup($groupId);
    }

    private function setGroupByName(string $groupName) {
        $this->setGroup(null,$groupName);
    }

    private function setGroup(int $groupId = null, string $groupName = null) {

        if (is_null($this->groupId)) {
            if (!is_null($groupId) || !is_null($groupName)) {
                if (!is_null($groupId)) {
                    $this->groupId = $groupId;
                } else {
                    $cleverReachGroups = $this->getGroups();
                    foreach ($cleverReachGroups as $cleverReachGroup) {
                        if ($cleverReachGroup->name == $groupName) {
                            $this->groupId = $cleverReachGroup->id;
                            return;
                        }
                    }
                    $group = $this->createGroup($groupName);
                    $this->groupId = $group->id;
                }
            } else {
                $this->error = true;
                $this->errorMessages["setGroup"] = $this->setErrorMessage("Set Group Error");
            }
        }
    }

    private function checkGlobalAttributes() {

    }

    private function checkGroupAttributes() {

    }

    public function getReceiversForGroup(int $groupId) {

    }

    public function upsertReceivers(array $receivers) {

        foreach ($receivers as &$receiver) {

            $actualReceiverResponse = $this->getReceiverByMail($receiver["email"]);
            if ($this->getResponseStatusCode($actualReceiverResponse) == 200) {
                $actualReceiver = $this->getResponseBodyContent($actualReceiverResponse);
                if ($receiver["activated"] == 0 && $actualReceiver->activated <> 0) {
                    $receiver["activated"] = $actualReceiver->activated;
                }
                if ($actualReceiver->registered <> 0) {
                    $receiver["registered"] = $actualReceiver->registered;
                }
                if (!isset($actualReceiver->global_attributes->coupon) || $actualReceiver->global_attributes->coupon == "") {
                    $receiver["global_attributes"]["coupon"] = array(
                        "value"                 =>      get_nl_coupon(),
                        "type"                  =>      'text',
                        "description"           =>      'Coupon',
                    );
                }
            } else {
                $receiver["global_attributes"]["coupon"] = array(
                    "value"                 =>      get_nl_coupon(),
                    "type"                  =>      'text',
                    "description"           =>      'Coupon',
                );
            }

            $globalAttributesToAdd = array_diff_key($receiver["global_attributes"],$this->globalAttributes);
            foreach ($globalAttributesToAdd as $attributeName => $attributeValue) {
                $result = $this->addAttribute($attributeName,$attributeValue["description"],$attributeValue["type"]);
                if ($result) {
                    $this->globalAttributes[$result->name] = $result->id;
                }
            }
            foreach ($receiver["global_attributes"] as $key => &$value) {
                $value = $value["value"];
            }
            unset($value);

            $groupAttributesToAdd = array_diff_key($receiver["attributes"],$this->groupAttributes);
            foreach ($groupAttributesToAdd as $attributeName => $attributeValue) {
                $result = $this->addAttribute($attributeName,$attributeValue["description"],$attributeValue["type"],true);
                if ($result) {
                    $this->groupAttributes[$result->name] = $result->id;
                }
            }
            foreach ($receiver["attributes"] as $key => &$value) {
                $value = $value["value"];
            }
            unset($value);

        }
        unset($receiver);

        $this->addTokenParam = true;
        $response = $this->request('POST','groups.json/' . $this->groupId . '/receivers/upsert',["postdata" => $receivers]);
        if ($this->getResponseStatusCode($response) == 200) {
            $this->sendOptInMail($receivers);
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["upsertReceivers"] = $this->setErrorMessage("Upsert Receivers Error",$response);
        return false;
    }

    public function unsubscrineReceivers(array $receivers) {
        foreach ($receivers as $receiver) {
            $this->addTokenParam = true;
            $response = $this->request('PUT','groups.json/' . $this->groupId . '/receivers/' . urlencode($receiver["email"]) . '/setinactive',["postdata" => $receivers]);
            if ($this->getResponseStatusCode($response) != 200) {
                $this->error = true;
                $this->errorMessages["unsubscrineReceivers"] = $this->setErrorMessage("Unsubscribe Receivers Error",$response);
            }
        }
        if ($this->error) {
            return false;
        }
        return true;
    }

    private function sendOptInMail($receivers) {
        if (!is_null($this->optInFormId)) {
            foreach ($receivers as $receiver) {
                if ($receiver["activated"] == 0 && $receiver["send_doi_mail"]) {
                    $this->addTokenParam = true;
                    $data = array(
                        "email" => $receiver["email"],
                        "groups_id" => $this->groupId,
                        "doidata" => array(
                            "user_ip" => $_SERVER["REMOTE_ADDR"],
                            "referer" => $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
                            "user_agent" => $_SERVER["HTTP_USER_AGENT"]
                        )
                    );
                    $this->addTokenParam = true;
                    $response = $this->request('POST','forms.json/' . $this->optInFormId . '/send/activate',$data);
                    if ($this->getResponseStatusCode($response) != 200) {
                        $this->error = true;
                        $this->errorMessages["sendOptInMail"] = $this->setErrorMessage("sendOptInMail Error",$response);
                    }
                }
            }
        } else {
            $this->error = true;
            $this->errorMessages["sendOptInMail"] = $this->setErrorMessage("No DOI form found");
        }
    }

    public function addReceiverOrderItemById(int $id, string $orderItem) {
        return $this->addReceiverOrderItem($id,$orderItem);
    }

    public function addReceiverOrderItemByMail(string $email, string $orderItem) {
        return $this->addReceiverOrderItem($email,$orderItem);
    }

    private function addReceiverOrderItem($identifier, string $orderItem) {

    }

    public function getReceiverById(int $id) {
        return $this->getReceiver($id);
    }

    public function getReceiverByMail(string $email) {
        return $this->getReceiver($email);
    }

    private function getReceiver($identifier) {
        $this->addTokenParam = true;
        $this->hasUrlParams = true;
        $response = $this->request('GET','receivers.json/' . $identifier . '?group_id=' . $this->groupId);
        return $response;
    }

    public function deleteReceiverById(int $id) {
        return $this->deleteReceiver($id);
    }

    public function deleteReceiverByMail(string $email) {
        return $this->deleteReceiver($email);
    }

    private function deleteReceiver($identifier) {

    }

    public function createAttribute(array $attributeData, int $groupId = null) {

    }

    public function activateReceiverById(int $id, int $groupId) {
        return $this->activateReceiver($id,$groupId);
    }

    public function activateReceiverByMail(string $email, int $groupId) {
        return $this->activateReceiver($email,$groupId);
    }

    private function activateReceiver($identifier, int $groupId) {

    }

    public function deActivateReceiverById(int $id, int $groupId) {
        return $this->deActivateReceiver($id,$groupId);
    }

    public function deActivateReceiverByMail(string $email, int $groupId) {
        return $this->deActivateReceiver($email,$groupId);
    }

    private function deActivateReceiver($identifier, int $groupId) {

    }

    public function sendForm(int $formId, bool $activate, array $data) {

    }

    public function getGlobalAttributes() {
        $this->addTokenParam = true;
        $response = $this->request('GET','attributes.json');
        if ($this->getResponseStatusCode($response) == 200) {
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["getGlobalAttributes"] = $this->setErrorMessage("Get Global Atrributes Error",$response);
        return false;
    }

    private function setGlobalAttributes() {
        $attributes = $this->getGlobalAttributes();
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $this->globalAttributes[$attribute->name] = $attribute->id;
            }
        }
    }

    public function getGroupAttributes() {
        $this->addTokenParam = true;
        $this->hasUrlParams = true;
        $response = $this->request('GET','attributes.json?group_id=' . $this->groupId . '');
        if ($this->getResponseStatusCode($response) == 200) {
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["getLocalAttributes"] = $this->setErrorMessage("Get Local Atrributes Error",$response);
        return false;
    }

    private function setLocalAttributes() {
        $attributes = $this->getGroupAttributes();
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $this->groupAttributes[$attribute->name] = $attribute->id;
            }
        }
    }

    public function addAttribute($name,$description,$type,$groupBound = false) {
        $this->addTokenParam = true;
        $dataAr = ["name" => $name, "description" => $description, "type" => $type];
        if ($groupBound) {
            $dataAr["groups_id"] = $this->groupId;
        }
        $response = $this->request('POST','attributes.json',$dataAr);
        if ($this->getResponseStatusCode($response) == 200) {
            return $this->getResponseBodyContent($response);
        }
        $this->error = true;
        $this->errorMessages["addAttribute"] = $this->setErrorMessage("Add Atrributes Error",$response);
        return false;
    }

}