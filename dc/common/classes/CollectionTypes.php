<?php
namespace DynCom\dc\common\classes;
/**
 * Class CollectionTypes
 * @package DynCom\dc\common\classes
 */
class CollectionTypes {

    private static $collectionTypes = NULL;

    /**
     * CollectionTypes constructor.
     */
    private function __construct() {
    }

    /**
     * @return array|null
     */
    public static function get() {
        if (self::$collectionTypes === NULL) {
            $translation = \DynCom\dc\common\classes\Registry::get('translation');

            self::$collectionTypes = array(
                1 => array( // Text
                    'description' => $translation->get("text"),
                    'class'       => 'inhalt_text',
                    'code'        => 'textcontent'
                ),

                2 => array( // textarea
                    'description' => $translation->get("textarea"),
                    'class'       => 'inhalt_slideshow',
                    'code'        => 'textarea'
                ),

                3 => array( // Datum
                    'description' => $translation->get("date"),
                    'class'       => 'inhalt_contact',
                    'code'        => 'date'
                ),

                4 => array( // Link
                    'description' => $translation->get("link"),
                    'class'       => 'inhalt_accordion',
                    'code'        => 'link'
                ),

                5 => array( // Bild
                    'description' => $translation->get("image"),
                    'class'       => 'inhalt_files',
                    'code'        => 'image'
                ),

            );
        }

        return self::$collectionTypes;
    }
}