<?php
/**
 * Class MailSystem
 *
 * The class handling mails
 * Uses ClassPHPMailer.
 *
 * @author    Daniel Kruse
 * @copyright 2016 Breitmeister Entertainment
 * @name      MailSystem.php
 * @package   tightCMS/cernel
 * @version   1.0.0
 */

namespace tightCMS\cernel;

use tightCMS\cernel\abstracts\Cernel;
use tightCMS\cernel\interfaces\MailSystem as MailSystemInterface;
use PHPMailer;

/**
 * Class MailSystem
 */
class MailSystem extends Cernel implements MailSystemInterface
{
    /**
     * The Mailsender
     * @var PHPMailer
     */
    private $mailtool;

    /**
     * Instanciates the PHPMailer
     */
    function __construct()
    {
        $this->mailtool = new \PHPMailer();
    }

    /**
     * Releases the PHPMailer
     */
    function __destruct()
    {
        unset($this->mailtool);
    }

    /**
     * Sets the username, password and server
     *
     * @param string $username Username
     * @param string $password Password
     * @param string $server   Server to send from
     * @return boolean|string Success or error message
     */
    public function setAuth($username, $password, $server)
    {
        if ((! empty($username)) && (! empty($password)) && (! empty($server))) {
            $this->mailtool->IsSMTP();
            $this->mailtool->SMTPAuth = true;
            $this->mailtool->Host     = $server;
            $this->mailtool->Username = $username;
            $this->mailtool->Password = $password;

            return $this->mailtool->SmtpConnect();
        }
        
        return '#0013: Error setting authentification: empty!';
    }

    /**
     * Sets the sender of the mail
     *
     * @param string $from     Sender mailadress
     * @param string $fromName Sender name
     * @return boolean|string Success or error message
     */
    public function setSender($from, $fromName)
    {
        if ((! empty($from)) && (! empty($fromName))) {
            $this->mailtool->From     = $from;
            $this->mailtool->FromName = $fromName;
            
            return true;
        } 
        
        return '#0014: Error setting sender: empty!';
    }

    /**
     * Sets a secondary receiver
     *
     * @param string $sendCc     Adress of the CC
     * @param string $sendCcName Name of the CC
     * @return boolean|string Success or error message
     */
    public function setCc($sendCc, $sendCcName)
    {
        if ((! empty($sendCc)) && (! empty($sendCcName))) {
            return $this->mailtool->AddCC($sendCc, $sendCcName);
        }
        
        return '#0015: Error setting carbon copy: empty!';
    }

    /**
     * Sends the content for the receiver
     *
     * @param string|array $sendTo     Emailadress of the receiver
     * @param string|array $sendToName Name of the receiver
     * @param string       $title      Title of the mail
     * @param string       $content    Content of the mail
     * @return boolean success?
     */
    public function sendMail($sendTo, $sendToName, $title, $content)
    {
        if (
            (! empty($sendTo)) &&
            (! empty($sendToName)) &&
            (! empty($content)) &&
            (! empty($title))
        ) {
            // Set addresses
            $this->mailtool->ClearAddresses();
            if ((\is_array($sendTo)) && (\is_array($sendToName))) {
                $this->addMultipleReceiver($sendTo, $sendToName);
            } else {
                $this->mailtool->AddAddress($sendTo, $sendToName);
            }
            // Set content
            $this->mailtool->Subject = $title;
            $this->mailtool->Body    = $content;
            $this->mailtool->AltBody = strip_tags($content);

            return $this->mailtool->Send();
        }

        return $this->sendError($sendTo, $content, $title);
    }
    
    /**
     * Adds multiple addresses to the mailtool
     * 
     * @param array $sendTo     List of mail addresses
     * @param array $sendToName List of names
     */
    private function addMultipleReceiver(array $sendTo, array $sendToName) 
    {
        foreach ($sendTo as $index => $adresse) {
            $this->mailtool->AddAddress($adresse, $sendToName[$index]);
        }
    }
    
    /**
     * Returns the error of sending
     * 
     * @param string $sendTo  Send to
     * @param string $content Content
     * @param string $title   Title
     * @return string
     */
    private function sendError($sendTo, $content, $title)
    {
        if (empty($sendTo)) {
            return '#0016: Error setting receiver: empty!';
        }
        if (empty($content)) {
            return '#0016: Error setting content: empty!';
        }
        if (empty($title)) {
            return '#0016: Error setting title: empty!';
        }
    }
}
