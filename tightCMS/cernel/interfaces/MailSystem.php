<?php
/**
 * Interface MailSystem
 *
 * The interface to send mails
 * TighCMS will only call these functions
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      MailSystem.php
 * @package   tightCMS/cernel/interface
 * @version   1.0.0
 */

namespace tightCMS\cernel\interfaces;

use tightCMS\cernel\interfaces\Cernel;

/**
 * interface MailSystem
 */
interface MailSystem extends Cernel
{
    /**
     * Sets the username, password and server
     *
     * @param string $username Username
     * @param string $password Password
     * @param string $server   Server to send from
     * @return boolean|string Success or error message
     */
    public function setAuth($username, $password, $server);

    /**
     * Sets the sender of the mail
     *
     * @param string $from     Sender mailadress
     * @param string $fromName Sender name
     * @return boolean|string Success or error message
     */
    public function setSender($from, $fromName);

    /**
     * Sets a secondary receiver
     *
     * @param string $sendCc     Adress of the CC
     * @param string $sendCcName Name of the CC
     * @return boolean|string Success or error message
     */
    public function setCc($sendCc, $sendCcName);

    /**
     * Sends the content for the receiver
     *
     * @param string|array $sendTo     Emailadress of the receiver
     * @param string|array $sendToName Name of the receiver
     * @param string       $title      Title of the mail
     * @param string       $content    Content of the mail
     * @return boolean success?
     */
    public function sendMail($sendTo, $sendToName, $title, $content);
}
