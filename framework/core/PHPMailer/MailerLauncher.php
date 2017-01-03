<?php
namespace framework\core\PHPMailer;

/**
 * Class MailerLauncher
 * @package framework\core\PHPMailer
 * 
 * @author Arnaout Slimen <arnaout.slimen@sbc.tn>
 */
class MailerLauncher
{
    /**
     * @var \PHPMailer
     */
    private $mailer;

    public function __construct()
    {
        $this->mailer = new \PHPMailer();
        $this->mailer->isSMTP();
        $this->mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->isHTML(true);
    }

    /**
     * Set sender's adress and name
     * @param string $senderMail
     * @param string $senderName
     * @throws \phpmailerException
     */
    public function setSenderInfo($senderMail, $senderName){
        $this->mailer->setFrom($senderMail, $senderName);
    }

    /**
     * Set receiver's adress
     * @param string $receiver
     */
    public function setReceiver($receiver){
        $this->mailer->addAddress($receiver);
    }

    /**
     * Set mail subject
     * @param string $subject
     */
    public function subject($subject){
        $this->mailer->Subject = $subject; 
    }

    /**
     * Set message to send
     * @param string $message
     */
    public function message($message){
        $this->mailer->Body = $message;
    }

    /**
     * Deliver message
     * @return bool
     * @throws \phpmailerException
     */
    public function deliver(){
        return $this->mailer->send();
    }

    /**
     * @return string
     */
    public function errors(){
        return $this->mailer->ErrorInfo;
    }
}