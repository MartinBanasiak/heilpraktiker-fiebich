<?php
namespace DynCom\dc\dcShop\classes;

use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\common\classes\UploadedFileTypeCheckingService;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 28.04.2016
 * Time: 17:17
 */
class CustomizationService
{

    const KEY_CUSTOMIZE_ID = 'shop_item_customize_id';
    const KEY_CUSTOMIZATION_VALUE = 'customization_value';
    const KEY_ITEM_ID = 'item_id';
    const KEY_CUSTOMIZATION_HASH = 'customization_hash';
    const KEY_FIELD_LENGTH = 'field_length';
    const KEY_FIELD_TYPE = 'field_type';
    const KEY_FIELD_NAME = 'field_name';
    const KEY_FIELD_DESCRIPTION = 'field_description';
    const KEY_VALUE = 'value';
    const POSTFIELD_REGEX = '/inputcustomize_([0-9]+)_([0-9]+)/';
    const CUSTOMIZATION_DEF_QUERY = '
        SELECT * FROM shop_item_customize
        WHERE id = :id
    ';
    const BASKET_CUSTOMIZATION_GET_QUERY = '
        SELECT * FROM shop_user_basket_customize
        WHERE customization_hash = :customization_hash
    ';
    const BASKET_CUSTOMIZATION_GET_QUERY_BY_FIELD_TYPE = '
        SELECT * FROM shop_user_basket_customize
        WHERE customization_hash = :customization_hash and field_type = :field_type
    ';
    const CUSTOMIZATION_INSERT_QUERY = '
        INSERT INTO shop_user_basket_customize
        SET field_length = :field_length,
            field_type = :field_type,
            field_name = :field_name,
            field_description = :field_description,
            `value` = :customization_value,
            customization_hash = :customization_hash,
            item_customization_id = :item_customization_id
            
    ';
    const CUSTOMIZATION_UPDATE_QUERY = '
        UPDATE shop_user_basket_customize
        SET field_length = :field_length,
            field_type = :field_type,
            field_name = :field_name,
            field_description = :field_description,
            `value` = :customization_value,
            customization_hash = :customization_hash,
            item_customization_id = :item_customization_id
        WHERE id = :id
    ';


    const BASKET_CUSTOMIZATION_UPDATE_VALUE_QUERY = '
        UPDATE shop_user_basket_customize
        SET 
            `value` = :customization_value
        WHERE id = :id
    ';

    const CUSTOMIZATION_SALES_LINE_INSERT_QUERY = '
        INSERT INTO shop_sales_line_customize
        SET sales_line_id = :sales_line_id,
            field_type = :field_type,
            field_name = :field_name,
            `value` = :customization_value,
            update_insert = :update_insert,
            to_delete = :to_delete        
    ';
    const CUSTOMIZATION_SALES_LINE_UPDATE_QUERY = '
        UPDATE shop_sales_line_customize
        SET sales_line_id = :sales_line_id,
            field_type = :field_type,
            field_name = :field_name,
            `value` = :customization_value,
            update_insert = :update_insert,
            to_delete = :to_delete
        WHERE id = :id
    ';
    const KEY_SALES_LINE_ID = 'sales_line_id';
    const KEY_UPDATE_INSERT = 'update_insert';
    const KEY_TO_DELETE = 'to_delete';
    const KEY_FIELD_INDEX = 'field_index';

    private $uploadFolder = '';
    const KEY_ITEM_CUSTOMIZATION_ID = 'item_customization_id';


    /**
     * @var PDOQueryWrapper
     */
    private $db;

    /**
     * @var UploadedFileTypeCheckingService
     */
    private $fileTypeChecker;


    /**
     * CustomizationService constructor.
     * @param PDOQueryWrapper $db
     * @param UploadedFileTypeCheckingService $fileTypeCheckingService
     */
    public function __construct(PDOQueryWrapper $db, UploadedFileTypeCheckingService $fileTypeCheckingService = null)
    {
        $this->uploadFolder = rtrim(dirname(dirname(dirname(dirname(__DIR__)))), '/') . DIRECTORY_SEPARATOR . 'userdata' . DIRECTORY_SEPARATOR . 'dcshop' . DIRECTORY_SEPARATOR . 'customize';
        $this->db = $db;
        $this->fileTypeChecker = $fileTypeCheckingService;
    }


    /**
     * @return array|string
     */
    public function handleCustomizationRequestAndGetHash()
    {
        $customizationDataFromRequest = $this->getCustomizationDataFromRequest();
        //echo "customizationDataFromRequest: ";
        //echo "<pre>";
        //var_dump($customizationDataFromRequest);
        //echo "</pre>";
        $preparedCustomizationData = $this->prepareCustomizationData($customizationDataFromRequest);
        /*echo "preparedCustomizationData: ";
        echo "<pre>";
        var_dump($preparedCustomizationData);
        echo "</pre>";*/

        $customizationHash = [];
        //SL+++
        /*if (array_key_exists(0,$preparedCustomizationData) && array_key_exists(self::KEY_CUSTOMIZATION_HASH,$preparedCustomizationData[0])) {
            $customizationHash = $preparedCustomizationData[0][self::KEY_CUSTOMIZATION_HASH];
        }*/
        //SL---
        //SL +++
        foreach ($preparedCustomizationData as $preparedCustomization) {
            if (is_array($preparedCustomization) && array_key_exists(self::KEY_CUSTOMIZATION_HASH, $preparedCustomization)) {
                $customizationHash[] = $preparedCustomization[self::KEY_CUSTOMIZATION_HASH];
            }
        }
        //SL ---
        /*echo "customizationHash: ";
        echo "<pre>";
        var_dump($customizationHash);
        echo "</pre>";*/
        $this->saveCustomizationData($preparedCustomizationData);
        if (!empty($customizationHash)) {
            return $customizationHash;
        }
        return '';
    }


    /**
     * @return array
     */
    public function getCustomizationDataFromRequest()
    {
        $customizationData = [];
        $itemID = filter_var($_GET['action_id'], FILTER_SANITIZE_NUMBER_INT);
        $this->populateCustomizationData($customizationData, $_POST, $itemID);
        $this->populateCustomizationData($customizationData, $_FILES, $itemID);
        return $customizationData;
    }

    /**
     * @param array $data
     * @param array $fromArray
     * @param $itemID
     */
    public function populateCustomizationData(array &$data, array &$fromArray, $itemID)
    {

        foreach ($fromArray as $key => $value) {
            $matches = null;
            if (preg_match(self::POSTFIELD_REGEX, $key, $matches)) {
                $currItemQtyIndex = $matches[1];
                $currCustFieldID = $matches[2];
                $customizationValue = filter_var($value, FILTER_SANITIZE_STRING);
                $data[$currItemQtyIndex][] = [
                    self::KEY_CUSTOMIZE_ID => $currCustFieldID,
                    self::KEY_CUSTOMIZATION_VALUE => $customizationValue,
                    self::KEY_ITEM_ID => $itemID,
                    self::KEY_FIELD_INDEX => $currCustFieldID,
                ];
            }
        }
    }

    /**
     * @param $qtyIndex
     * @param $fieldIndex
     * @return string
     */
    public function handleFileUploadForCustomizationFieldAndGetFilename($qtyIndex, $fieldIndex)
    {
        $dummy = '';
        if (!empty($_FILES)) {
            $fileKey = 'inputcustomize_' . (int)$qtyIndex . '_' . (int)$fieldIndex;
            if (!array_key_exists($fileKey, $_FILES)) {
                throw new \ErrorException('No file-upload in superglobal for key ' . $fileKey);
            }
            if (!empty($_FILES[$fileKey]['tmp_name'])) {
                $filePath = $_FILES[$fileKey]['tmp_name'];
                $extension = '';
                if ($this->fileTypeChecker->isFileAllowed($filePath, UploadedFileTypeCheckingService::TYPE_BMP_JPG_PNG_GIF, $extension)) {
                    $fileHash = hash_file('md5', $filePath);
                    $fileName = $fileHash.''.time() . '.' . $extension;
                    $targetPath = $this->uploadFolder . DIRECTORY_SEPARATOR . $fileName;
                    $success = move_uploaded_file($filePath, $targetPath);
                    if ($success) {
                        return $fileName;
                    }
                }
            }
        }
        return $dummy;
    }

    /**
     * @param array $array
     * @return array
     */
    public function prepareCustomizationData(array $array)
    {
        $customizationRows = [];
        $customizationDataString = '|';
        $itemID = 0;
        $i = 0;
        foreach ($array as $qtyIndex => $row) {
            foreach ($row as $subArray) {
                if (
                    !is_array($subArray)
                    || !array_key_exists(self::KEY_CUSTOMIZATION_VALUE, $subArray)
                    || !array_key_exists(self::KEY_CUSTOMIZE_ID, $subArray)
                    || !array_key_exists(self::KEY_ITEM_ID, $subArray)
                    || !array_key_exists(self::KEY_FIELD_INDEX, $subArray)
                ) {
                    throw new \InvalidArgumentException(
                        'Array must contain only subarrays containing fields ' . self::KEY_CUSTOMIZATION_VALUE . ', ' .
                        self::KEY_CUSTOMIZE_ID . ', ' . self::KEY_ITEM_ID . ' and ' . self::KEY_FIELD_INDEX
                    );
                }

                $itemID = (int)$subArray[self::KEY_ITEM_ID];
                if ($i === 0) {
                    $customizationDataString .= $itemID . '-|-';
                }
                $customization_id = $subArray[self::KEY_CUSTOMIZE_ID];
                $customization_value = $subArray[self::KEY_CUSTOMIZATION_VALUE];
                $fieldIndex = $subArray[self::KEY_FIELD_INDEX];
                $customization_data = $this->getCustomizationFieldSpecificationByID($customization_id);
                if (
                    !is_array($customization_data)
                    || !array_key_exists(self::KEY_FIELD_LENGTH, $customization_data)
                    || !array_key_exists(self::KEY_FIELD_TYPE, $customization_data)
                    || !array_key_exists(self::KEY_FIELD_NAME, $customization_data)
                    || !array_key_exists(self::KEY_FIELD_DESCRIPTION, $customization_data)
                ) {
                    throw new \ErrorException('Could not get shop_item_customization data for id ' . $customization_id);
                }
                if ((int)$customization_data[self::KEY_FIELD_TYPE] === 1) {
                    $filename = $this->handleFileUploadForCustomizationFieldAndGetFilename($qtyIndex, $fieldIndex);
                    $customization_value = $filename;
                }

                $customizationDataString .= $customization_id . '-|-' . $customization_value . '|';
                $basket_customization_row = [
                    'id' => null,
                    self::KEY_FIELD_LENGTH => $customization_data[self::KEY_FIELD_LENGTH],
                    self::KEY_FIELD_TYPE => $customization_data[self::KEY_FIELD_TYPE],
                    self::KEY_FIELD_NAME => $customization_data[self::KEY_FIELD_NAME],
                    self::KEY_FIELD_DESCRIPTION => $customization_data[self::KEY_FIELD_DESCRIPTION],
                    self::KEY_VALUE => $customization_value,
                    self::KEY_ITEM_CUSTOMIZATION_ID => $fieldIndex,
                    self::KEY_CUSTOMIZATION_HASH => ''
                ];
                $customizationRows[$i][] = $basket_customization_row;
            }
            //SL +++
            $customizationHashPerindex = md5($customizationDataString);
            $this->setCustomizationHashInRowsPerIndex($customizationHashPerindex, $customizationRows[$i]);
            //SL ---
            $i++;
        }
        $customizationRowsFlat = [];
        foreach ($customizationRows as $customizationRowQtyIndex) {
            foreach ($customizationRowQtyIndex as $row) {
                $customizationRowsFlat[] = $row;
            }
        }
        //SL +++
        //$customizationHash = md5($customizationDataString);
        //$this->setCustomizationHashInRows($customizationHash, $customizationRows);
        //SL ---

        return $customizationRowsFlat;
    }

    /**
     * @param array $array
     */
    public function saveCustomizationData(array $array)
    {
        foreach ($array as $row) {
            if (
                !is_array($row)
                || !array_key_exists(self::KEY_FIELD_LENGTH, $row)
                || !array_key_exists(self::KEY_FIELD_TYPE, $row)
                || !array_key_exists(self::KEY_FIELD_NAME, $row)
                || !array_key_exists(self::KEY_FIELD_DESCRIPTION, $row)
                || !array_key_exists(self::KEY_ITEM_CUSTOMIZATION_ID, $row)
            ) {
                throw new \InvalidArgumentException('Array must contain only sub-arrays with keys: ' . self::KEY_FIELD_LENGTH . ', ' .
                    self::KEY_FIELD_TYPE . ', ' . self::KEY_FIELD_NAME . ', ' .
                    self::KEY_FIELD_DESCRIPTION . ' and ' . self::KEY_ITEM_CUSTOMIZATION_ID);
            }
            $parameters = [
                [':' . self::KEY_FIELD_LENGTH, (int)$row[self::KEY_FIELD_LENGTH], \PDO::PARAM_INT],
                [':' . self::KEY_FIELD_TYPE, (int)$row[self::KEY_FIELD_TYPE], \PDO::PARAM_INT],
                [':' . self::KEY_FIELD_NAME, (string)$row[self::KEY_FIELD_NAME], \PDO::PARAM_STR],
                [':' . self::KEY_FIELD_DESCRIPTION, (string)$row[self::KEY_FIELD_DESCRIPTION], \PDO::PARAM_STR],
                [':' . self::KEY_CUSTOMIZATION_VALUE, (string)$row[self::KEY_VALUE], \PDO::PARAM_STR],
                [':' . self::KEY_CUSTOMIZATION_HASH, (string)$row[self::KEY_CUSTOMIZATION_HASH], \PDO::PARAM_STR],
                [':' . self::KEY_ITEM_CUSTOMIZATION_ID, (int)$row[self::KEY_ITEM_CUSTOMIZATION_ID], \PDO::PARAM_INT],
            ];

            if (isset($row['id']) && (int)$row['id'] > 0) {
                $parameters[] = [':id', (int)$row['id'], \PDO::PARAM_INT];
                $this->db->setQuery(self::CUSTOMIZATION_UPDATE_QUERY);
            } else {
                $this->db->setQuery(self::CUSTOMIZATION_INSERT_QUERY);
            }

            $this->db->prepareQuery();
            $this->db->bindParameters($parameters);
            if (!($this->db->executePreparedStatement() && $this->db->getNoOfAffectedRows() === 1)) {
                throw new \ErrorException('Could not save customization. Error: ' . $this->db->getErrorMessage());
            }
        }
    }

    /**
     * @param $customizationHash
     * @param array $customizationRows
     */
    private function setCustomizationHashInRows($customizationHash, array &$customizationRows)
    {
        foreach ($customizationRows as &$customizationRow) {
            if (is_array($customizationRow) && array_key_exists(self::KEY_CUSTOMIZATION_HASH, $customizationRow)) {
                $customizationRow[self::KEY_CUSTOMIZATION_HASH] = $customizationHash;
            }
        }
        unset($customizationRow);
    }

    /**
     * @param $customizationHash
     * @param array $customizationRows
     */
    private function setCustomizationHashInRowsPerIndex($customizationHash, array &$customizationRows)
    {
        foreach ($customizationRows as &$customizationRow) {
            if (is_array($customizationRow) && array_key_exists(self::KEY_CUSTOMIZATION_HASH, $customizationRow)) {
                $customizationRow[self::KEY_CUSTOMIZATION_HASH] = $customizationHash;
            }
        }
        unset($customizationRow);
    }


    /**
     * @param $hash
     * @return array
     */
    public function getCustomizationDataFromHash($hash)
    {
        $this->db->setQuery(self::BASKET_CUSTOMIZATION_GET_QUERY);
        $this->db->prepareQuery();
        $parameters = [
            [':' . self::KEY_CUSTOMIZATION_HASH, (string)$hash, \PDO::PARAM_STR],
        ];
        $this->db->bindParameters($parameters);
        $result = [];
        if ($this->db->executePreparedStatement() && $this->db->getNoOfReturnedRows() > 0) {
            $result = $this->db->getResultArray();
        }
        return $result;
    }

    /**
     * @param $id
     * @return array
     */
    public function getCustomizationFieldSpecificationByID($id)
    {
        $this->db->setQuery(self::CUSTOMIZATION_DEF_QUERY);
        $this->db->prepareQuery();
        $parameters = [
            [':id', (int)$id, \PDO::PARAM_INT]
        ];
        $this->db->bindParameters($parameters);
        $result = [];
        if ($this->db->executePreparedStatement() && $this->db->getNoOfReturnedRows() === 1) {
            $result = $this->db->getResultArray()[0];
        }
        return $result;
    }

    /**
     * @param array $array
     */
    public function saveCustomizationSalesLines(array $array)
    {
        foreach ($array as $row) {
            if (
                !is_array($row)
                || !array_key_exists(self::KEY_SALES_LINE_ID, $row)
                || !array_key_exists(self::KEY_FIELD_TYPE, $row)
                || !array_key_exists(self::KEY_FIELD_NAME, $row)
                || !array_key_exists('value', $row)
                || !array_key_exists(self::KEY_UPDATE_INSERT, $row)
                || !array_key_exists(self::KEY_TO_DELETE, $row)
            ) {
                throw new \InvalidArgumentException('Array must contain only sub-arrays with keys: ' . self::KEY_SALES_LINE_ID . ', ' .
                    self::KEY_FIELD_TYPE . ', ' . self::KEY_FIELD_NAME . ', value, ' .
                    self::KEY_UPDATE_INSERT . ' and ' . self::KEY_TO_DELETE);
            }
            $parameters = [
                [':' . self::KEY_SALES_LINE_ID, (int)$row[self::KEY_SALES_LINE_ID], \PDO::PARAM_INT],
                [':' . self::KEY_FIELD_TYPE, (int)$row[self::KEY_FIELD_TYPE], \PDO::PARAM_INT],
                [':' . self::KEY_FIELD_NAME, (string)$row[self::KEY_FIELD_NAME], \PDO::PARAM_STR],
                [':' . self::KEY_CUSTOMIZATION_VALUE, (string)$row['value'], \PDO::PARAM_STR],
                [':' . self::KEY_UPDATE_INSERT, (int)$row[self::KEY_UPDATE_INSERT], \PDO::PARAM_INT],
                [':' . self::KEY_TO_DELETE, (int)$row[self::KEY_TO_DELETE], \PDO::PARAM_INT],
            ];
            if (isset($row['id']) && (int)$row['id'] > 0) {
                $parameters[] = [':id', (int)$row['id'], \PDO::PARAM_INT];
                $this->db->setQuery(self::CUSTOMIZATION_SALES_LINE_UPDATE_QUERY);
            } else {
                $this->db->setQuery(self::CUSTOMIZATION_SALES_LINE_INSERT_QUERY);
            }
            $query = $this->db->getQuery();
            $this->db->prepareQuery();
            $this->db->bindParameters($parameters);
            if (!($this->db->executePreparedStatement() && $this->db->getNoOfAffectedRows() === 1)) {
                throw new \ErrorException('Could not save customization sales line. Error: ' . $this->db->getErrorMessage());
            }
        }
    }


    /**
     * @param array $array
     */
    public function updateCustomizationValue($id, $value)
    {
        $parameters = [

            [':' . self::KEY_CUSTOMIZATION_VALUE, (string)$value, \PDO::PARAM_STR],
            [':id', (int)$id, \PDO::PARAM_INT],
        ];

        $this->db->setQuery(self::BASKET_CUSTOMIZATION_UPDATE_VALUE_QUERY);

        $this->db->prepareQuery();
        $this->db->bindParameters($parameters);
        if (!($this->db->executePreparedStatement())) {
            throw new \ErrorException('Could not save customization. Error: ' . $this->db->getErrorMessage());
        }
    }

    /**
     * @param $hash
     * @return array
     */
    public function getCustomizationDataFromHashAndFieldType($hash, $fieldType)
    {
        $this->db->setQuery(self::BASKET_CUSTOMIZATION_GET_QUERY_BY_FIELD_TYPE);
        $this->db->prepareQuery();
        $parameters = [
            [':' . self::KEY_CUSTOMIZATION_HASH, (string)$hash, \PDO::PARAM_STR],
            [':' . self::KEY_FIELD_TYPE, (int)$fieldType, \PDO::PARAM_INT],
        ];
        $this->db->bindParameters($parameters);
        $result = [];
        if ($this->db->executePreparedStatement() && $this->db->getNoOfReturnedRows() > 0) {
            $result = $this->db->getResultArray();
        }
        return $result;
    }

    public function deleteUserBasketCustomizationFile($itemHash)
    {
        $itemData = $this->getCustomizationDataFromHashAndFieldType($itemHash, 1);
        $oldFileName = $itemData[0]['value'];
        $oldFilePath = $GLOBALS['shop_setup']['uploaddir_customize'] . "/" . $oldFileName;
        if (file_exists("../../" . $oldFilePath)) {
            unlink("../../" . $oldFilePath);
        }
        $this->updateCustomizationValue($itemData[0]['id'], "");
    }


}