<?php
namespace DynCom\dc\common\classes;
use finfo;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 29.04.2016
 * Time: 11:17
 */
class UploadedFileTypeCheckingService
{
    const TYPE_BMP_JPG_PNG_GIF = 0;
    const TYPE_NO_PHP_JS = 1;
    const TYPE_ZIP = 2;

    private $allowedValidationTypes = [
        self::TYPE_BMP_JPG_PNG_GIF,
        self::TYPE_NO_PHP_JS,
        self::TYPE_ZIP,
    ];
	
	/**
	* @var UploadedFileTypeCheckingService
	*/
	private static $instance;
	
    private static $type_bmp_jpg_png_gif_mime = [
        'bmp' => 'image/bmp',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'gif' => 'image/gif',
		'png' => 'image/png',
    ];

    private static $type_no_php_js_mime = [
        'js' => 'application/javascript',
        'plain' => 'text/plain',
    ];

    private static $type_zip_mime = [
        'application/x-compressed' => 'zip',
        'application/x-zip-compressed' => 'zip',
        'application/zip' => 'zip',
        'multipart/x-zip' => 'zip',
    ];


    /**
     * @param $validationType
     * @return \Closure
     */
    private function getValidationFunctionForType($validationType) {
        if ($validationType === self::TYPE_BMP_JPG_PNG_GIF) {
            return function($filePath, &$extension) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
				$fileInfo = $finfo->file($filePath);
				$ext = array_search(
                        $fileInfo,
                        self::$type_bmp_jpg_png_gif_mime,
                        true);				
                if (false === $ext) {
                    return false;
                }
				if (null !== $extension) {
					$extension = $ext;
				} 
				return true;
            };
        } elseif ($validationType === self::TYPE_NO_PHP_JS) {
            return function($filePath, &$extension) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
				$fileInfo = $finfo->file($filePath);
                if (false === $ext = array_search(
                        $fileInfo,
                        self::$type_no_php_js_mime,
                        true)
                ) {					
					if (null !== $extension) {
						$extension = $ext;
					}
                    return true;
                }
                return false;
            };
        } elseif ($validationType === self::TYPE_ZIP) {
            return function($filePath, &$extension) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $fileInfo = $finfo->file($filePath);
                if (!(array_key_exists($fileInfo,self::$type_zip_mime) && self::$type_zip_mime[$fileInfo] === $extension))
                {
                    return false;
                }
                if (null !== $extension) {
                    $extension = self::$type_zip_mime[$fileInfo];
                }
                return true;
            };
        }else {
            return function ($filePath) {
                return false;
            };
        }
    }

    /**
     * @param $pathToFile
     * @param $validationType
     * @param $extension
     * @return mixed
     */
    public function isFileAllowed($pathToFile, $validationType, &$extension) {
        if (!file_exists($pathToFile) || !is_readable($pathToFile) || !in_array($validationType,$this->allowedValidationTypes,true)) {
            throw new \InvalidArgumentException('Parameters must specify a valid path to a readable file and an allowed validation type.');
        }       
        $validationFunction = $this->getValidationFunctionForType($validationType);		
        $isValid = $validationFunction($pathToFile,$extension);
		return $isValid;
		
    }

    /**
     * @param $filePath
     * @param $validationType
     * @param $extension
     * @return mixed
     */
    public static function checkFileAgainstValidationType($filePath, $validationType, &$extension)
	{
		if (null === self::$instance) {
			self::$instance = new static();
		}
		return self::$instance->isFileAllowed($filePath,$validationType,$extension);
	}
}