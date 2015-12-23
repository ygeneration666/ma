<?php
/**
 * @package     Mautic
 * @copyright   2015 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Modified from
 * @see         https://github.com/barbushin/php-imap
 * @author      Barbushin Sergey http://linkedin.com/in/barbushin
 * @copyright   BSD (three-clause)
 */

namespace Mautic\EmailBundle\MonitoredEmail;

use Mautic\CoreBundle\Factory\MauticFactory;
use stdClass;

class Mailbox
{

    protected $imapPath;
    protected $imapFullPath;
    protected $imapStream;
    protected $imapFolder = 'INBOX';
    protected $imapOptions = 0;
    protected $imapRetriesNum = 0;
    protected $imapParams = array();
    protected $serverEncoding = 'UTF-8';
    protected $attachmentsDir;
    protected $settings;
    protected $isGmail = false;
    protected $mailboxes;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;

        $this->mailboxes = $this->factory->getParameter('monitored_email');

        if (isset($this->mailboxes['general'])) {
            $this->settings = $this->mailboxes['general'];
        } else {
            $this->settings = array(
                'host'      => '',
                'port'      => '',
                'password'  => '',
                'user'      => '',
                'encryption' => ''
            );
        }

        // Check that cache attachments directory exists
        $cacheDir             = $factory->getSystemPath('cache');
        $this->attachmentsDir = $cacheDir . '/attachments';

        if (!file_exists($this->attachmentsDir)) {
            mkdir($this->attachmentsDir);
        }

        if ($this->settings['host'] == 'imap.gmail.com') {
            $this->isGmail = true;
        }
    }

    /**
     * Returns if a mailbox is configured
     *
     * @param null $bundleKey
     * @param null $folderKey
     *
     * @return bool
     * @throws \Exception
     */
    public function isConfigured($bundleKey = null, $folderKey = null) {
        if ($bundleKey !== null) {
            try {
                $this->switchMailbox($bundleKey, $folderKey);
            } catch (\Exception $e) {
                
                return false;
            }
        }

        return (
            !empty($this->settings['host']) &&
            !empty($this->settings['port']) &&
            !empty($this->settings['user']) &&
            !empty($this->settings['password'])
        );
    }

    /**
     * Switch to another configured monitored mailbox
     *
     * @param        $bundle
     * @param string $mailbox
     *
     * @throws \Exception
     */
    public function switchMailbox($bundle, $mailbox = '')
    {
        $key = $bundle . (!empty($mailbox) ? '_' . $mailbox : '');

        if (isset($this->mailboxes[$key])) {
            $this->settings = (!empty($this->mailboxes[$key]['override_settings'])) ? $this->mailboxes[$key] : $this->mailboxes['general'];
            $this->imapFolder = $this->mailboxes[$key]['folder'];
            $this->setImapPath();
        } else {
            throw new \Exception($key . ' not found');
        }
    }

    /**
     * Returns if this is a Gmail connection
     *
     * @return mixed
     */
    public function isGmail()
    {
        return $this->isGmail();
    }

    /**
     * Set imap path based on mailbox settings
     */
    public function setImapPath()
    {
        $paths              = $this->getImapPath($this->settings);
        $this->imapPath     = $paths['path'];
        $this->imapFullPath = $paths['full'];
    }

    /**
     * @param $settings
     *
     * @return array
     */
    public function getImapPath($settings)
    {
        /**
         * @var $host
         * @var $port
         * @var $encryption
         * @var $folder
         * @var $user
         * @var $password
         */
        extract($settings);
        if (!isset($encryption)) {
            $encryption = (!empty($ssl)) ? '/ssl' : '';
        }
        $path     = "{{$host}:{$port}/imap{$encryption}}";
        $fullPath = $path;

        if (isset($folder)) {
            $fullPath .= $folder;
        }

        return array('path' => $path, 'full' => $fullPath);
    }

    /**
     * Override mailbox settings
     *
     * @param array     $settings
     */
    public function setMailboxSettings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        $this->isGmail = ($this->settings['host'] == 'imap.gmail.com');

        $this->setImapPath();
    }

    /**
     * Get settings
     *
     * @param        $bundle
     * @param string $mailbox
     *
     * @return mixed
     * @throws \Exception
     */
    public function getMailboxSettings($bundle = null, $mailbox = '')
    {
        if ($bundle == null) {

            return $this->settings;
        }

        $key = $bundle . (!empty($mailbox) ? '_' . $mailbox : '');

        if (isset($this->mailboxes[$key])) {
            $settings = (!empty($this->mailboxes[$key]['override_settings'])) ? $this->mailboxes[$key] : $this->mailboxes['general'];

            $settings['folder']    = $this->mailboxes[$key]['folder'];
            $this->setImapPath();

            $imapPath              = $this->getImapPath($settings);
            $settings['imap_path'] = $imapPath['full'];
        } else {
            throw new \Exception($key . ' not found');
        }

        return $settings;
    }

    /**
     * Set custom connection arguments of imap_open method. See http://php.net/imap_open
     *
     * @param int   $options
     * @param int   $retriesNum
     * @param array $params
     */
    public function setConnectionArgs($options = 0, $retriesNum = 0, array $params = null)
    {
        $this->imapOptions    = $options;
        $this->imapRetriesNum = $retriesNum;
        $this->imapParams     = $params;
    }

    /**
     * Switch to another box
     *
     * @param $folder
     */
    public function switchFolder($folder)
    {
        if ($folder != $this->imapFolder) {
            $this->imapFullPath = $this->imapPath.$folder;
            $this->imapFolder   = $folder;
        }

        $this->getImapStream();
    }

    /**
     * Get IMAP mailbox connection stream
     *
     * @return null|resource
     */
    public function getImapStream()
    {
        if (!$this->isConnected()) {
            $this->imapStream = $this->initImapStream();
        } else {
            @imap_reopen($this->imapStream, $this->imapFullPath);
        }

        return $this->imapStream;
    }

    /**
     * @return resource
     * @throws \Exception
     */
    protected function initImapStream()
    {
        imap_timeout(IMAP_OPENTIMEOUT, 15);
        $imapStream = @imap_open(
            $this->imapFullPath,
            $this->settings['user'],
            $this->settings['password'],
            $this->imapOptions,
            $this->imapRetriesNum,
            $this->imapParams
        );
        if (!$imapStream) {
            throw new \Exception('Connection error: '.imap_last_error());
        }

        return $imapStream;
    }

    /**
     * Check if the stream is connected
     *
     * @return bool
     */
    protected function isConnected()
    {
        return ($this->isConfigured() && $this->imapStream && is_resource($this->imapStream) && @imap_ping($this->imapStream));
    }

    /**
     * Get information about the current mailbox.
     *
     * Returns the information in an object with following properties:
     *  Date - current system time formatted according to RFC2822
     *  Driver - protocol used to access this mailbox: POP3, IMAP, NNTP
     *  Mailbox - the mailbox name
     *  Nmsgs - number of mails in the mailbox
     *  Recent - number of recent mails in the mailbox
     *
     * @return stdClass
     */
    public function checkMailbox()
    {
        return imap_check($this->getImapStream());
    }

    /**
     * Creates a new mailbox specified by mailbox.
     *
     * @return bool
     */

    public function createMailbox()
    {
        return imap_createmailbox($this->getImapStream(), imap_utf7_encode($this->imapFullPath));
    }

    /**
     * Gets status information about the given mailbox.
     *
     * This function returns an object containing status information.
     * The object has the following properties: messages, recent, unseen, uidnext, and uidvalidity.
     *
     * @return stdClass if the box doesn't exist
     */

    public function statusMailbox()
    {
        return imap_status($this->getImapStream(), $this->imapFullPath, SA_ALL);
    }


    /**
     * Gets listing the folders
     *
     * This function returns an object containing listing the folders.
     * The object has the following properties: messages, recent, unseen, uidnext, and uidvalidity.
     *
     * @return array listing the folders
     */

    public function getListingFolders()
    {
        static $folders = array();

        if (!isset($folders[$this->imapFullPath]) && $this->isConfigured()) {
            $tempFolders = @imap_list($this->getImapStream(), $this->imapPath, "*");

            if (!empty($tempFolders)) {
                foreach ($tempFolders as $key => $folder) {
                    $folder            = str_replace($this->imapPath, "", imap_utf8($folder));
                    $tempFolders[$key] = $folder;
                }
            } else {
                $tempFolders = array();
            }

            $folders[$this->imapFullPath] = $tempFolders;
        }

        return $folders[$this->imapFullPath];
    }

    /**
     * Fetch unread messages
     *
     * @param null $folder
     *
     * @return array
     */
    public function fetchUnread($folder = null)
    {
        if ($folder !== null) {
            $this->switchFolder($folder);
        }

        return $this->searchMailBox('UNSEEN');
    }

    /**
     * This function performs a search on the mailbox currently opened in the given IMAP stream.
     * For example, to match all unanswered mails sent by Mom, you'd use: "UNANSWERED FROM mom".
     * Searches appear to be case insensitive. This list of criteria is from a reading of the UW
     * c-client source code and may be incomplete or inaccurate (see also RFC2060, section 6.4.4).
     *
     * @param string $criteria String, delimited by spaces, in which the following keywords are allowed. Any multi-word arguments (e.g. FROM "joey
     *                         smith") must be quoted. Results will match all criteria entries.
     *                         ALL - return all mails matching the rest of the criteria
     *                         ANSWERED - match mails with the \\ANSWERED flag set
     *                         BCC "string" - match mails with "string" in the Bcc: field
     *                         BEFORE "date" - match mails with Date: before "date"
     *                         BODY "string" - match mails with "string" in the body of the mail
     *                         CC "string" - match mails with "string" in the Cc: field
     *                         DELETED - match deleted mails FLAGGED - match mails with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
     *                         FROM "string" - match mails with "string" in the From: field
     *                         KEYWORD "string" - match mails with "string" as a keyword
     *                         NEW - match new mails
     *                         OLD - match old mails
     *                         ON "date" - match mails with Date: matching "date"
     *                         RECENT - match mails with the \\RECENT flag set
     *                         SEEN - match mails that have been read (the \\SEEN flag is set)
     *                         SINCE "date" - match mails with Date: after "date"
     *                         SUBJECT "string" - match mails with "string" in the Subject:
     *                         TEXT "string" - match mails with text "string"
     *                         TO "string" - match mails with "string" in the To:
     *                         UNANSWERED - match mails that have not been answered
     *                         UNDELETED - match mails that are not deleted UNFLAGGED - match mails that are not flagged
     *                         UNKEYWORD "string" - match mails that do not have the keyword "string"
     *                         UNSEEN - match mails which have not been read yet
     *
     * @return array Mails ids
     */
    public function searchMailbox($criteria = 'ALL')
    {
        $mailsIds = imap_search($this->getImapStream(), $criteria, SE_UID);

        return $mailsIds ? $mailsIds : array();
    }

    /**
     * Save mail body.
     *
     * @param        $mailId
     * @param string $filename
     *
     * @return bool
     */
    public function saveMail($mailId, $filename = 'email.eml')
    {
        return imap_savebody($this->getImapStream(), $filename, $mailId, "", FT_UID);
    }

    /**
     * Marks mails listed in mailId for deletion.
     *
     * @param $mailId
     *
     * @return bool
     */
    public function deleteMail($mailId)
    {
        return imap_delete($this->getImapStream(), $mailId, FT_UID);
    }

    /**
     * Move mail to another box
     *
     * @param $mailId
     * @param $mailBox
     *
     * @return bool
     */
    public function moveMail($mailId, $mailBox)
    {
        return imap_mail_move($this->getImapStream(), $mailId, $mailBox, CP_UID) && $this->expungeDeletedMails();
    }

    /**
     * Deletes all the mails marked for deletion by imap_delete(), imap_mail_move(), or imap_setflag_full().
     *
     * @return bool
     */
    public function expungeDeletedMails()
    {
        return imap_expunge($this->getImapStream());
    }

    /**
     * Add the flag \Seen to a mail.
     *
     * @param $mailId
     *
     * @return bool
     */
    public function markMailAsRead($mailId)
    {
        return $this->setFlag(array($mailId), '\\Seen');
    }

    /**
     * Remove the flag \Seen from a mail.
     *
     * @param $mailId
     *
     * @return bool
     */
    public function markMailAsUnread($mailId)
    {
        return $this->clearFlag(array($mailId), '\\Seen');
    }

    /**
     * Add the flag \Flagged to a mail.
     *
     * @param $mailId
     *
     * @return bool
     */
    public function markMailAsImportant($mailId)
    {
        return $this->setFlag(array($mailId), '\\Flagged');
    }

    /**
     * Add the flag \Seen to a mails.
     *
     * @param $mailIds
     *
     * @return bool
     */
    public function markMailsAsRead(array $mailIds)
    {
        return $this->setFlag($mailIds, '\\Seen');
    }

    /**
     * Remove the flag \Seen from some mails.
     *
     * @param $mailIds
     *
     * @return bool
     */
    public function markMailsAsUnread(array $mailIds)
    {
        return $this->clearFlag($mailIds, '\\Seen');
    }

    /**
     * Add the flag \Flagged to some mails.
     *
     * @param $mailIds
     *
     * @return bool
     */
    public function markMailsAsImportant(array $mailIds)
    {
        return $this->setFlag($mailIds, '\\Flagged');
    }

    /**
     * Causes a store to add the specified flag to the flags set for the mails in the specified sequence.
     *
     * @param array  $mailsIds
     * @param string $flag which you can set are \Seen, \Answered, \Flagged, \Deleted, and \Draft as defined by RFC2060.
     *
     * @return bool
     */
    public function setFlag(array $mailsIds, $flag)
    {
        return imap_setflag_full($this->getImapStream(), implode(',', $mailsIds), $flag, ST_UID);
    }

    /**
     * Cause a store to delete the specified flag to the flags set for the mails in the specified sequence.
     *
     * @param array  $mailsIds
     * @param string $flag which you can set are \Seen, \Answered, \Flagged, \Deleted, and \Draft as defined by RFC2060.
     *
     * @return bool
     */
    public function clearFlag(array $mailsIds, $flag)
    {
        return imap_clearflag_full($this->getImapStream(), implode(',', $mailsIds), $flag, ST_UID);
    }

    /**
     * Fetch mail headers for listed mails ids
     *
     * Returns an array of objects describing one mail header each. The object will only define a property if it exists. The possible properties are:
     *  subject - the mails subject
     *  from - who sent it
     *  to - recipient
     *  date - when was it sent
     *  message_id - Mail-ID
     *  references - is a reference to this mail id
     *  in_reply_to - is a reply to this mail id
     *  size - size in bytes
     *  uid - UID the mail has in the mailbox
     *  msgno - mail sequence number in the mailbox
     *  recent - this mail is flagged as recent
     *  flagged - this mail is flagged
     *  answered - this mail is flagged as answered
     *  deleted - this mail is flagged for deletion
     *  seen - this mail is flagged as already read
     *  draft - this mail is flagged as being a draft
     *
     * @param array $mailsIds
     *
     * @return array
     */
    public function getMailsInfo(array $mailsIds)
    {
        $mails = imap_fetch_overview($this->getImapStream(), implode(',', $mailsIds), FT_UID);
        if (is_array($mails) && count($mails)) {
            foreach ($mails as &$mail) {
                if (isset($mail->subject)) {
                    $mail->subject = $this->decodeMimeStr($mail->subject, $this->serverEncoding);
                }
                if (isset($mail->from)) {
                    $mail->from = $this->decodeMimeStr($mail->from, $this->serverEncoding);
                }
                if (isset($mail->to)) {
                    $mail->to = $this->decodeMimeStr($mail->to, $this->serverEncoding);
                }
            }
        }

        return $mails;
    }

    /**
     * Get information about the current mailbox.
     *
     * Returns an object with following properties:
     *  Date - last change (current datetime)
     *  Driver - driver
     *  Mailbox - name of the mailbox
     *  Nmsgs - number of messages
     *  Recent - number of recent messages
     *  Unread - number of unread messages
     *  Deleted - number of deleted messages
     *  Size - mailbox size
     *
     * @return object Object with info | FALSE on failure
     */

    public function getMailboxInfo()
    {
        return imap_mailboxmsginfo($this->getImapStream());
    }

    /**
     * Gets mails ids sorted by some criteria
     *
     * Criteria can be one (and only one) of the following constants:
     *  SORTDATE - mail Date
     *  SORTARRIVAL - arrival date (default)
     *  SORTFROM - mailbox in first From address
     *  SORTSUBJECT - mail subject
     *  SORTTO - mailbox in first To address
     *  SORTCC - mailbox in first cc address
     *  SORTSIZE - size of mail in octets
     *
     * @param int  $criteria
     * @param bool $reverse
     *
     * @return array Mails ids
     */
    public function sortMails($criteria = SORTARRIVAL, $reverse = true)
    {
        return imap_sort($this->getImapStream(), $criteria, $reverse, SE_UID);
    }

    /**
     * Get mails count in mail box
     *
     * @return int
     */
    public function countMails()
    {
        return imap_num_msg($this->getImapStream());
    }

    /**
     * Retrieve the quota settings per user
     *
     * @return array - FALSE in the case of call failure
     */
    protected function getQuota()
    {
        return imap_get_quotaroot($this->getImapStream(), 'INBOX');
    }

    /**
     * Return quota limit in KB
     *
     * @return int - FALSE in the case of call failure
     */
    public function getQuotaLimit()
    {
        $quota = $this->getQuota();
        if (is_array($quota)) {
            $quota = $quota['STORAGE']['limit'];
        }

        return $quota;
    }

    /**
     * Return quota usage in KB
     *
     * @return int - FALSE in the case of call failure
     */
    public function getQuotaUsage()
    {
        $quota = $this->getQuota();
        if (is_array($quota)) {
            $quota = $quota['STORAGE']['usage'];
        }

        return $quota;
    }

    /**
     * Get mail data
     *
     * @param      $mailId
     * @param bool $markAsSeen
     *
     * @return Mail
     */
    public function getMail($mailId, $markAsSeen = true)
    {
        $head = imap_rfc822_parse_headers(imap_fetchheader($this->getImapStream(), $mailId, FT_UID));

        $mail              = new Message();
        $mail->id          = $mailId;
        $mail->date        = date('Y-m-d H:i:s', isset($head->date) ? strtotime(preg_replace('/\(.*?\)/', '', $head->date)) : time());
        $mail->subject     = isset($head->subject) ? $this->decodeMimeStr($head->subject, $this->serverEncoding) : null;
        $mail->fromName    = isset($head->from[0]->personal) ? $this->decodeMimeStr($head->from[0]->personal, $this->serverEncoding) : null;
        $mail->fromAddress = strtolower($head->from[0]->mailbox.'@'.$head->from[0]->host);

        if (isset($head->to)) {
            $toStrings = array();
            foreach ($head->to as $to) {
                if (!empty($to->mailbox) && !empty($to->host)) {
                    $toEmail            = strtolower($to->mailbox.'@'.$to->host);
                    $toName             = isset($to->personal) ? $this->decodeMimeStr($to->personal, $this->serverEncoding) : null;
                    $toStrings[]        = $toName ? "$toName <$toEmail>" : $toEmail;
                    $mail->to[$toEmail] = $toName;
                }
            }
            $mail->toString = implode(', ', $toStrings);
        }

        if (isset($head->cc)) {
            foreach ($head->cc as $cc) {
                $mail->cc[strtolower($cc->mailbox.'@'.$cc->host)] = isset($cc->personal) ? $this->decodeMimeStr($cc->personal, $this->serverEncoding)
                    : null;
            }
        }

        if (isset($head->reply_to)) {
            foreach ($head->reply_to as $replyTo) {
                $mail->replyTo[strtolower($replyTo->mailbox.'@'.$replyTo->host)] = isset($replyTo->personal) ? $this->decodeMimeStr(
                    $replyTo->personal,
                    $this->serverEncoding
                ) : null;
            }
        }

        $mailStructure = imap_fetchstructure($this->getImapStream(), $mailId, FT_UID);

        if (empty($mailStructure->parts)) {
            $this->initMailPart($mail, $mailStructure, 0, $markAsSeen);
        } else {
            foreach ($mailStructure->parts as $partNum => $partStructure) {
                $this->initMailPart($mail, $partStructure, $partNum + 1, $markAsSeen);
            }
        }

        return $mail;
    }

    /**
     * @param Message      $mail
     * @param              $partStructure
     * @param              $partNum
     * @param bool|true    $markAsSeen
     * @param bool|false   $isDsn
     */
    protected function initMailPart(Message $mail, $partStructure, $partNum, $markAsSeen = true, $isDsn = false)
    {
        $options = FT_UID;
        if (!$markAsSeen) {
            $options |= FT_PEEK;
        }
        $data = $partNum
            ? imap_fetchbody($this->getImapStream(), $mail->id, $partNum, $options)
            : imap_body(
                $this->getImapStream(),
                $mail->id,
                $options
            );

        if ($partStructure->encoding == 1) {
            $data = imap_utf8($data);
        } elseif ($partStructure->encoding == 2) {
            $data = imap_binary($data);
        } elseif ($partStructure->encoding == 3) {
            $data = imap_base64($data);
        } elseif ($partStructure->encoding == 4) {
            $data = quoted_printable_decode($data);
        }

        $params = $this->getParameters($partStructure);

        // attachments
        $attachmentId = $partStructure->ifid
            ? trim($partStructure->id, " <>")
            : (isset($params['filename']) || isset($params['name']) ? mt_rand().mt_rand() : null);

        if ($attachmentId) {
            if (empty($params['filename']) && empty($params['name'])) {
                $fileName = $attachmentId.'.'.strtolower($partStructure->subtype);
            } else {
                $fileName = !empty($params['filename']) ? $params['filename'] : $params['name'];
                $fileName = $this->decodeMimeStr($fileName, $this->serverEncoding);
                $fileName = $this->decodeRFC2231($fileName, $this->serverEncoding);
            }
            $attachment       = new Attachment();
            $attachment->id   = $attachmentId;
            $attachment->name = $fileName;
            if ($this->attachmentsDir) {
                $replace              = array(
                    '/\s/'                   => '_',
                    '/[^0-9a-zа-яіїє_\.]/iu' => '',
                    '/_+/'                   => '_',
                    '/(^_)|(_$)/'            => '',
                );
                $fileSysName          = preg_replace(
                    '~[\\\\/]~',
                    '',
                    $mail->id.'_'.$attachmentId.'_'.preg_replace(array_keys($replace), $replace, $fileName)
                );
                $attachment->filePath = $this->attachmentsDir.DIRECTORY_SEPARATOR.$fileSysName;
                file_put_contents($attachment->filePath, $data);
            }
            $mail->addAttachment($attachment);
        } else {
            if (!empty($params['charset'])) {
                $data = $this->convertStringEncoding($data, $params['charset'], $this->serverEncoding);
            }

            if ($partStructure->type == 0 && $data) {
                if (strtolower($partStructure->subtype) == 'plain') {
                    $mail->textPlain .= $data;
                } else {
                    $mail->textHtml .= $data;
                }
            } elseif ($partStructure->type == 1 && $partStructure->ifsubtype && $partStructure->subtype == 'REPORT' && isset($params['REPORT-TYPE']) && $params['REPORT-TYPE'] == 'delivery-status') {
                    $mail->dsnMessage = trim($data);
                    $isDsn = true;
            } elseif ($partStructure->type == 2 && $data) {
                if ($isDsn) {
                    $mail->dsnReport = $data;
                } else {
                    $mail->textPlain .= trim($data);
                }
            }
        }
        if (!empty($partStructure->parts)) {
            foreach ($partStructure->parts as $subPartNum => $subPartStructure) {
                if ($partStructure->type == 2 && $partStructure->subtype == 'RFC822') {
                    $this->initMailPart($mail, $subPartStructure, $partNum, $markAsSeen, $isDsn);
                } else {
                    $this->initMailPart($mail, $subPartStructure, $partNum.'.'.($subPartNum + 1), $markAsSeen, $isDsn);
                }
            }
        }
    }

    /**
     * @param $partStructure
     *
     * @return array
     */
    protected function getParameters($partStructure)
    {
        $params = array();
        if (!empty($partStructure->parameters)) {
            foreach ($partStructure->parameters as $param) {
                $params[strtolower($param->attribute)] = $param->value;
            }
        }
        if (!empty($partStructure->dparameters)) {
            foreach ($partStructure->dparameters as $param) {
                $paramName = strtolower(preg_match('~^(.*?)\*~', $param->attribute, $matches) ? $matches[1] : $param->attribute);
                if (isset($params[$paramName])) {
                    $params[$paramName] .= $param->value;
                } else {
                    $params[$paramName] = $param->value;
                }
            }
        }

        return $params;
    }

    /**
     * @param        $string
     * @param string $charset
     *
     * @return string
     */
    protected function decodeMimeStr($string, $charset = 'utf-8')
    {
        $newString = '';
        $elements  = imap_mime_header_decode($string);
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i]->charset == 'default') {
                $elements[$i]->charset = 'iso-8859-1';
            }
            $newString .= $this->convertStringEncoding($elements[$i]->text, $elements[$i]->charset, $charset);
        }

        return $newString;
    }

    /**
     * @param $string
     *
     * @return bool
     */
    protected function isUrlEncoded($string)
    {
        $hasInvalidChars = preg_match('#[^%a-zA-Z0-9\-_\.\+]#', $string);
        $hasEscapedChars = preg_match('#%[a-zA-Z0-9]{2}#', $string);

        return !$hasInvalidChars && $hasEscapedChars;
    }

    /**
     * @param        $string
     * @param string $charset
     *
     * @return string
     */
    protected function decodeRFC2231($string, $charset = 'utf-8')
    {
        if (preg_match("/^(.*?)'.*?'(.*?)$/", $string, $matches)) {
            $encoding = $matches[1];
            $data     = $matches[2];
            if ($this->isUrlEncoded($data)) {
                $string = $this->convertStringEncoding(urldecode($data), $encoding, $charset);
            }
        }

        return $string;
    }

    /**
     * Converts a string from one encoding to another.
     *
     * @param string $string
     * @param string $fromEncoding
     * @param string $toEncoding
     *
     * @return string Converted string if conversion was successful, or the original string if not
     */
    protected function convertStringEncoding($string, $fromEncoding, $toEncoding)
    {
        $convertedString = null;
        if ($string && $fromEncoding != $toEncoding) {
            $convertedString = @iconv($fromEncoding, $toEncoding.'//IGNORE', $string);
            if (!$convertedString && extension_loaded('mbstring')) {
                $convertedString = @mb_convert_encoding($string, $toEncoding, $fromEncoding);
            }
        }

        return $convertedString ?: $string;
    }

    /**
     * Close IMAP connection
     */
    protected function disconnect()
    {
        if ($this->isConnected()) {
            @imap_close($this->imapStream, CL_EXPUNGE);
        }
    }

    /**
     * Disconnect on destruct
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}
