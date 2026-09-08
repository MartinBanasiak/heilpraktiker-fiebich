<?php
namespace DynCom\dc\regionalization;

/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 13.10.2016
 * Time: 22:24
 */
class PDORegionalizedTextProvider implements RegionalizedTextProvider
{
    protected const REGIONALIZED_TEXT_QUERY = '
        SELECT `text` FROM regionalized_texts WHERE `code` = :text_code AND `locale` = :text_locale ORDER BY id ASC LIMIT 1 
    ';

    /**
     * @var PDO
     */
    protected $db;

    protected $locale = '';

    protected $cachedTexts = [];

    /**
     * PDORegionalizedTextProvider constructor.
     * @param PDO $db
     * @param string $locale
     */
    public function __construct(PDO $db, $locale)
    {
        $this->db = $db;
        $this->locale = $locale;
    }

    public function getRegionalizedText($name)
    {
        if (array_key_exists($name,$this->cachedTexts)) {
            return $this->cachedTexts[$name];
        }
        $stmt = $this->db->prepare(static::REGIONALIZED_TEXT_QUERY);
        $stmt->bindValue(':text_code',$name,PDO::PARAM_STR);
        $stmt->bindValue(':text_locale',$this->locale,PDO::PARAM_STR);
        $stmt->execute();
        $textRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (is_array($textRow) && array_key_exists('text',$textRow)) {
            $this->cachedTexts[$name] = $textRow['text'];
        } else {
            $this->cachedTexts[$name] = $name;
        }
        return $this->cachedTexts[$name];


    }


}