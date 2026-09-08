<?php
namespace DynCom\dc\workerqueue\workers\email;

use Exception;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 21.07.2016
 * Time: 14:02
 */
class SendEmailErrorException extends Exception
{
    const ERROR_CODE = 500;

    private static $messageTemplate = 'An email was not successfully sent to one or more recipients. Sender-address was [%s]. Subject was [%s]. Total list of recipients was [%s]. List of failed recipients is [%s].';

    /**
     * SendEmailErrorException constructor.
     * @param string $senderAddress The mail-address of the sendera
     * @param string $subject The mail-address of the subject
     * @param array $intendedRecipientsList The list of intended recipients
     * @param array $failedRecipientsList The list of failed recipients
     * @param Exception|null $previous
     */
    public function __construct(
        $senderAddress,
        $subject,
        array $intendedRecipientsList,
        array $failedRecipientsList,
        Exception $previous = null
    )
    {
        $intendedRecipientsListCleaned = [];
        foreach ($intendedRecipientsList as $key => $value) {
            if (is_int($key) || ctype_digit($key)) {
                $intendedRecipientsListCleaned[] = $value;
            } elseif (filter_var($key, FILTER_VALIDATE_EMAIL)) {
                $intendedRecipientsList[] = $key;
            }
        }
        $message = sprintf(
            self::$messageTemplate,
            $senderAddress,
            $subject,
            implode(', ', $intendedRecipientsListCleaned),
            implode(', ', $failedRecipientsList)
        );
        parent::__construct($message, self::ERROR_CODE, $previous);

    }
}