<?php

class SwiftMailerComponent extends Object {

    var $transport;

    #  //called after Controller::beforeFilter()
    function startup() {

        require_once( APP_PATH . 'vendors' . DS . 'swift'.DS.'lib'.DS.'swift_required.php'); 
        Configure::load('yfi');
        $host = Configure::read('email.smtpHost');
        $port = Configure::read('email.smtpPort');
        $this->transport = Swift_SmtpTransport::newInstance($host, $port);
        if(Configure::read('email.smtpUsername') != ''){
            $this->transport->setUsername(Configure::read('email.smtpUsername'));
            $this->transport->setUsername(Configure::read('email.smtpPassword'));
        }
        if(Configure::read('email.smtpEncryption') != ''){
            $this->transport->setEncryption(Configure::read('email.smtpEncryption'));
        }
    }


    function sendMessage ($to,$from,$subject,$body){

        $mailer = Swift_Mailer::newInstance($this->transport);
        $message = Swift_Message::newInstance($this->transport);
        $message->setSubject($subject);
        $message->setFrom(array($from));
        //$message->setTo(&$to); //Remove the call time reference
        //http://sourceforge.net/p/hotcakes/discussion/728132/thread/6d26d090/?limit=25#25a4
        $message->setTo($to); //Remove the call time reference
        $message->setBody($body);
        $result = $mailer->batchSend($message);

    }

    function sendAttachment($to,$from,$subject,$body,$filename){
        $mailer = Swift_Mailer::newInstance($this->transport);
        $message = Swift_Message::newInstance($this->transport);
        //Create the attachment
        // * Note that you can technically leave the content-type parameter out
        $attachment = Swift_Attachment::fromPath($filename, "application/pdf");  
        //Attach it to the message
        $message->attach($attachment);
        $message->setSubject($subject);
        $message->setFrom(array($from));
        //$message->setTo(&$to); //Remove the call time reference
        //http://sourceforge.net/p/hotcakes/discussion/728132/thread/6d26d090/?limit=25#25a4
        $message->setTo($to);
        $message->setBody($body);
        $result = $mailer->send($message);
    }
}

/*
http://127.0.0.1/c2/yfi_cake/nas/json_state/16
*/



?>
