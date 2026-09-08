<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 16.10.2016
 * Time: 18:14
 */
namespace DynCom\dc\workerqueue\workers\email;

use Swift_Attachment;
use Swift_Message;


/**
 * Class SendEmailJobPayload
 * @package DynCom\dc\workerqueue\email
 */
class SendEmailJobPayload
{

    protected const KEY_SENDER_NAME = 'sender_name';
    protected const KEY_SENDER_MAIL = 'sender_mail';
    protected const KEY_RECIPIENTS = 'recipients';
    protected const KEY_CCS = 'ccs';
    protected const KEY_BCCS = 'bccs';
    protected const KEY_MAIL_SUBJECT = 'mail_subject';
    protected const KEY_MAIL_BODY = 'mail_body';
    protected const KEY_MAIL_BODY_CONTENT_TYPE = 'mail_body_content_type';
    protected const KEY_MAIL_PARTS = 'mail_parts';
    protected const KEY_ATTACHMENTS = 'attachments';
    protected const KEY_MAIL_PART_CONTENT_TYPE = 'mail_part_content_type';
    protected const KEY_MAIL_PART_CONTENT = 'mail_part_content';
    protected const KEY_ATTACHMENT_PATH = 'attachment_path';
    protected const KEY_RECIPIENT_MAIL = 'recipient_mail';
    protected const KEY_CC_MAIL = 'cc_mail';
    protected const KEY_CC_NAME = 'cc_name';
    protected const KEY_BCC_MAIL = 'bcc_mail';
    protected const KEY_BCC_NAME = 'bcc_name';
    protected const KEY_RECIPIENT_NAME = 'recipient_name';
    private $senderMail;
    private $senderName;
    private $subject;
    private $body;
    private $contentType;
    private $recipients = [];
    private $ccs = [];
    private $bccs = [];
    private $attachments = [];
    private $parts = [];

    /**
     * SendEmailJobPayload constructor.
     * @param $senderMail
     * @param $primaryRecipientMail
     * @param $subject
     * @param $body
     * @param $contentType
     */
    public function __construct(string $senderMail, string $primaryRecipientMail, string $subject, string $body, string $contentType)
    {
        $this->senderMail = $senderMail;
        $this->subject = $subject;
        $this->body = $body;
        $this->contentType = $contentType;
        $this->recipients[] = [self::KEY_RECIPIENT_MAIL => $primaryRecipientMail, self::KEY_RECIPIENT_NAME => ''];
    }

    /**
     * @param array $payload
     * @return SendEmailJobPayload
     */
    public static function fromJobPayloadArray(array $payload): SendEmailJobPayload
    {
        $senderMail = $payload[self::KEY_SENDER_MAIL];
        $senderName = $payload[self::KEY_SENDER_NAME];
        $recipients = $payload[self::KEY_RECIPIENTS];
        $ccs = $payload[self::KEY_CCS];
        $bccs = $payload[self::KEY_BCCS];
        $subject = $payload[self::KEY_MAIL_SUBJECT];
        $body = $payload[self::KEY_MAIL_BODY];
        $contentType = $payload[self::KEY_MAIL_BODY_CONTENT_TYPE];
        $parts = $payload[self::KEY_MAIL_PARTS];
        $attachments = $payload[self::KEY_ATTACHMENTS];

        $primaryRecipientMail = $recipients[0][self::KEY_RECIPIENT_MAIL];

        $instance = new static($senderMail, $primaryRecipientMail, $subject, $body, $contentType);
        $instance->setSenderName($senderName);
        $instance->addRecipients($recipients);
        $instance->addCcs($ccs);
        $instance->addBccs($bccs);
        $instance->addParts($parts);
        $instance->addAttachments($attachments);
        return $instance;
    }

    /**
     * @param array $recipients
     */
    public function addRecipients(array $recipients): void
    {
        foreach ($recipients as $recipientArr) {
            if (array_key_exists(self::KEY_RECIPIENT_MAIL, $recipientArr) && array_key_exists(self::KEY_RECIPIENT_NAME, $recipientArr) && !empty($recipientArr[self::KEY_RECIPIENT_MAIL])) {
                $this->addRecipient($recipientArr[self::KEY_RECIPIENT_NAME], $recipientArr[self::KEY_RECIPIENT_MAIL]);
            }
        }
        return;
    }

    /**
     * @param string $name
     * @param string $email
     */
    public function addRecipient(string $name, string $email): void
    {
        $exists = false;
        foreach ($this->recipients as $recipientArr) {
            if ($recipientArr[self::KEY_RECIPIENT_MAIL] === $email) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->recipients[] = [self::KEY_RECIPIENT_NAME => $name, self::KEY_RECIPIENT_MAIL => $email];
        }
        return;
    }

    /**
     * @param array $ccs
     */
    public function addCcs(array $ccs): void
    {
        foreach ($ccs as $ccArr) {
            if (array_key_exists(self::KEY_CC_MAIL, $ccArr) && array_key_exists(self::KEY_CC_NAME, $ccArr) && !empty($ccArr[self::KEY_CC_MAIL])) {
                $this->addCc($ccArr[self::KEY_CC_NAME], $ccArr[self::KEY_CC_MAIL]);
            }
        }
        return;
    }

    /**
     * @param $name
     * @param $email
     */
    public function addCc(string $name, string $email): void
    {
        $exists = false;
        foreach ($this->ccs as $ccArr) {
            if ($ccArr[self::KEY_CC_MAIL] === $email) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->ccs[] = [self::KEY_CC_NAME => $name, self::KEY_CC_MAIL => $email];
        }
        return;
    }

    /**
     * @param array $bccs
     */
    public function addBccs(array $bccs): void
    {
        foreach ($bccs as $bccArr) {
            if (array_key_exists(self::KEY_BCC_MAIL, $bccArr) && array_key_exists(self::KEY_BCC_NAME, $bccArr) && !empty($bccArr[self::KEY_BCC_MAIL])) {
                $this->addBcc($bccArr[self::KEY_BCC_NAME], $bccArr[self::KEY_BCC_MAIL]);
            }
        }
        return;
    }

    /**
     * @param $name
     * @param $email
     */
    public function addBcc(string $name, string $email): void
    {
        $exists = false;
        foreach ($this->bccs as $bccArr) {
            if ($bccArr[self::KEY_BCC_MAIL] === $email) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->bccs[] = [self::KEY_BCC_NAME => $name, self::KEY_BCC_MAIL => $email];
        }
        return;
    }

    /**
     * @param array $parts
     */
    public function addParts(array $parts): void
    {
        foreach ($parts as $partArr) {
            if (array_key_exists(self::KEY_MAIL_PART_CONTENT_TYPE, $partArr) && array_key_exists(self::KEY_MAIL_PART_CONTENT, $partArr)) {
                $this->addPart($partArr[self::KEY_MAIL_PART_CONTENT_TYPE], $partArr[self::KEY_MAIL_PART_CONTENT]);
            }
        }
        return;
    }

    /**
     * @param $contentType
     * @param $content
     */
    public function addPart(string $contentType, string $content): void
    {
        $exists = false;
        foreach ($this->parts as $partArr) {
            if ($partArr[self::KEY_MAIL_PART_CONTENT_TYPE] === $contentType && $partArr[self::KEY_MAIL_PART_CONTENT] === $content) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->parts[] = [self::KEY_MAIL_PART_CONTENT_TYPE => $contentType, self::KEY_MAIL_PART_CONTENT => $content];
        }
        return;
    }

    /**
     * @param array $attachments
     */
    public function addAttachments(array $attachments): void
    {
        foreach ($attachments as $attachmentArr) {
            if (array_key_exists(self::KEY_ATTACHMENT_PATH, $attachmentArr) && !empty($attachmentArr[self::KEY_ATTACHMENT_PATH])) {
                $this->addAttachment($attachmentArr[self::KEY_ATTACHMENT_PATH]);
            }
        }
        return;
    }

    /**
     * @param $attachmentFilePath
     */
    public function addAttachment(string $attachmentFilePath): void
    {
        $exists = false;
        foreach ($this->attachments as $attachmentArr) {
            if ($attachmentArr[self::KEY_ATTACHMENT_PATH] === $attachmentFilePath) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->attachments[] = [self::KEY_ATTACHMENT_PATH => $attachmentFilePath];
        }
        return;
    }

    /**
     * @param string $name
     */
    public function removeRecipientByName(string $name): void
    {
        $recipients = $this->recipients;
        $currIndex = 0;
        foreach ($this->recipients as $recipientArr) {
            if ($recipientArr[self::KEY_RECIPIENT_NAME] === $name) {
                unset($recipients[$currIndex]);
            }
            $currIndex++;
        }
        $this->recipients = array_values($recipients);
        return;
    }

    /**
     * @param $email
     */
    public function removeRecipientByEmail(string $email): void
    {
        $recipients = $this->recipients;
        $currIndex = 0;
        foreach ($this->recipients as $recipientArr) {
            if ($recipientArr[self::KEY_RECIPIENT_MAIL] === $email) {
                unset($recipients[$currIndex]);
            }
            $currIndex++;
        }
        $this->recipients = array_values($recipients);
        return;
    }

    /**
     * @param $name
     */
    public function removeBccByName(string $name): void
    {
        $bccs = $this->bccs;
        $currIndex = 0;
        foreach ($this->bccs as $bccArr) {
            if ($bccArr[self::KEY_BCC_NAME] === $name) {
                unset($bccs[$currIndex]);
            }
            $currIndex++;
        }
        $this->bccs = array_values($bccs);
        return;
    }

    /**
     * @param $email
     */
    public function removeBccByEmail(string $email): void
    {
        $bccs = $this->bccs;
        $currIndex = 0;
        foreach ($this->bccs as $bccArr) {
            if ($bccArr[self::KEY_BCC_MAIL] === $email) {
                unset($bccs[$currIndex]);
            }
            $currIndex++;
        }
        $this->bccs = array_values($bccs);
        return;
    }

    /**
     * @param $name
     */
    public function removeCcByName(string $name): void
    {
        $ccs = $this->ccs;
        $currIndex = 0;
        foreach ($this->ccs as $ccArr) {
            if ($ccArr[self::KEY_CC_NAME] === $name) {
                unset($ccs[$currIndex]);
            }
            $currIndex++;
        }
        $this->ccs = array_values($ccs);
        return;
    }

    /**
     * @param $email
     */
    public function removeCcByEmail(string $email): void
    {
        $ccs = $this->ccs;
        $currIndex = 0;
        foreach ($this->ccs as $ccArr) {
            if ($ccArr[self::KEY_CC_MAIL] === $email) {
                unset($ccs[$currIndex]);
            }
            $currIndex++;
        }
        $this->ccs = array_values($ccs);
        return;
    }

    /**
     * @return string
     */
    public function getSenderMail(): string
    {
        return $this->senderMail;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param $name
     */
    public function setSenderName(string $name): void
    {
        $this->senderName = $name;
        return;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
        return;
    }

    /**
     * @return mixed
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return array
     */
    public function getCcs(): array
    {
        return $this->ccs;
    }

    /**
     * @return array
     */
    public function getBccs(): array
    {
        return $this->bccs;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return array
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param $body
     * @param $contentType
     */
    public function setBodyAndContentType(string $body, string $contentType): void
    {
        $this->body = $body;
        $this->contentType = $contentType;
        return;
    }

    /**
     * @param $attachmentFilePath
     */
    public function removeAttachment(string $attachmentFilePath): void
    {
        $collection = $this->attachments;
        $collectionIndex = 0;
        foreach ($this->attachments as $attachmentArr) {
            if ($attachmentArr[self::KEY_ATTACHMENT_PATH] == $attachmentFilePath) {
                unset($collection[$collectionIndex]);
            }
            $collectionIndex++;
        }
        $this->attachments = array_values($collection);
        return;
    }

    /**
     * @return array
     */
    public function getAsPayloadArray(): array
    {
        return [
            self::KEY_SENDER_NAME => $this->senderName,
            self::KEY_SENDER_MAIL => $this->senderMail,
            self::KEY_RECIPIENTS => $this->recipients,
            self::KEY_CCS => $this->ccs,
            self::KEY_BCCS => $this->bccs,
            self::KEY_MAIL_SUBJECT => $this->subject,
            self::KEY_MAIL_BODY => $this->body,
            self::KEY_MAIL_BODY_CONTENT_TYPE => $this->contentType,
            self::KEY_MAIL_PARTS => $this->parts,
            self::KEY_ATTACHMENTS => $this->attachments,
        ];
    }

    /**
     * @param Swift_Message $message
     */
    public function fillSwiftMessage(Swift_Message $message): void
    {
        if (!$this->isMinimallyValid()) {
            throw new \BadMethodCallException('Data was not minimally valid upon calling method to fill Swift_Message');
        }
        $senderMail = $this->senderMail;
        $senderName = $this->senderName;
        $from = $senderMail;
        if ($senderName) {
            $from = [$senderMail => $senderName];
        }
        $subject = $this->subject;
        $to = [];
        foreach ($this->recipients as $recipient) {
            $recipientMail = $recipient[static::KEY_RECIPIENT_MAIL];
            $recipientName = $recipient[static::KEY_RECIPIENT_NAME];
            if ($recipientName) {
                $to[$recipientMail] = $recipientName;
            } else {
                $to[] = $recipientMail;
            }
        }
        $cc = [];
        if (is_array($this->ccs) && count($this->ccs) > 0) {
            foreach ($this->ccs as $recipient) {
                $recipientMail = $recipient[static::KEY_CC_MAIL];
                $recipientName = $recipient[static::KEY_CC_NAME];
                if (filter_var($recipientMail, FILTER_VALIDATE_EMAIL)) {
                    if ($recipientName) {
                        $cc[$recipientMail] = $recipientName;
                    } else {
                        $cc[] = $recipientMail;
                    }
                }
            }
        }
        $bcc = [];
        if (is_array($this->bccs) && count($this->bccs) > 0) {
            foreach ($this->bccs as $recipient) {
                $recipientMail = $recipient[static::KEY_BCC_MAIL];
                $recipientName = $recipient[static::KEY_BCC_NAME];
                if (filter_var($recipientMail, FILTER_VALIDATE_EMAIL)) {
                    if ($recipientName) {
                        $bcc[$recipientMail] = $recipientName;
                    } else {
                        $bcc[] = $recipientMail;
                    }
                }
            }
        }
        $body = $this->body;
        $bodyContentType = $this->contentType;

        $attachments = [];
        if (is_array($this->attachments) && count($this->attachments) > 0) {
            foreach ($this->attachments as $attachmentArr) {
                $path = $attachmentArr[static::KEY_ATTACHMENT_PATH];
                if (file_exists($path) && is_readable($path)) {
                    $attachments[] = Swift_Attachment::fromPath($path);
                }
            }
        }

        $parts = [];
        if (is_array($this->parts) && count($this->parts) > 0) {
            foreach ($this->parts as $part) {
                if (
                    array_key_exists(static::KEY_MAIL_PART_CONTENT_TYPE, $part)
                    && array_key_exists(static::KEY_MAIL_PART_CONTENT, $part)
                    && mb_strlen($part[static::KEY_MAIL_PART_CONTENT_TYPE]) > 0
                    && mb_strlen($part[static::KEY_MAIL_PART_CONTENT]) > 0
                ) {
                    $parts[] = $part;
                }
            }
        }


        $message->setFrom($from);
        $message->setTo($to);
        if (count($cc) > 0) {
            $message->setCc($cc);
        }
        if (count($bcc) > 0) {
            $message->setBcc($to);
        }
        if (count($attachments) > 0) {
            foreach ($attachments as $path) {
                $message->attach($path);
            }
        }
        $message->setSubject($subject);
        $message->setBody($body, $bodyContentType);

        if (count($parts) > 0) {
            foreach ($parts as $part) {
                $message->addPart($part[static::KEY_MAIL_PART_CONTENT], $part[static::KEY_MAIL_PART_CONTENT_TYPE]);
            }
        }
        return;

    }

    /**
     * @param array|null $reasons
     * @return bool
     */
    public function isMinimallyValid(array &$reasons = null): bool
    {
        if ($reasons === null) {
            $reasons = [];
        }
        $isMinimallyValid = true;

        if (!filter_var($this->senderMail, FILTER_VALIDATE_EMAIL)) {
            $isMinimallyValid = false;
            $reasons[] = 'Sender-Mail [' . $this->senderMail . '] not valid.';
        }
        if (!(mb_strlen($this->subject) > 0)) {
            $isMinimallyValid = false;
            $reasons[] = 'Subject is empty.';
        }
        if (empty($this->body)) {
            $isMinimallyValid = false;
            $reasons[] = 'Body is empty.';
        }
        if (empty($this->contentType)) {
            $isMinimallyValid = false;
            $reasons[] = 'Content Type is empty.';
        }
        if (!(count($this->recipients) > 0) || !is_array($this->recipients[0]) || !array_key_exists(self::KEY_RECIPIENT_MAIL, $this->recipients[0]) || !filter_var($this->recipients[0][self::KEY_RECIPIENT_MAIL], FILTER_VALIDATE_EMAIL)) {
            $isMinimallyValid = false;
            $reasons[] = 'No valid recipients in [' . print_r($this->recipients, true) . '].';
        }
        return $isMinimallyValid;

    }

    /**
     * @param $name
     */
    public function setPrimaryRecipientName(string $name): void
    {
        if (array_key_exists(0, $this->recipients) && is_array($this->recipients[0])) {
            $this->recipients[0][self::KEY_RECIPIENT_NAME] = $name;
        }
        return;
    }

}