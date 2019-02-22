<?php
namespace OC\PlatformBundle\Antispam;

/**
 * @author Abdennassir
 *
 */
class OCAntispam
{
    
    private $mailer;
    private $local;
    private $minLength;
    
    public function  __construct(\Swift_Mailer $mailer,$local,$minLength){
        
        $this->mailer = $mailer;
        $this->local = $local;
        $this->minLength = (int) $minLength;
    }
    
    /**
     * Verfier si le text est un spam
     * @param String $text
     * @return boolean
     */
    
    public function isSpam($text) {
        return strlen($text)< $this->minLength;
    }
}

