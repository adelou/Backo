<?php
namespace App\AdminBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerService
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
    * @var \Swift_Mailer
    */
    protected $mailer;

    public function __construct(ContainerInterface $container)
    {
        $this->container     = $container;
        $this->mailer        = $this->container->get('mailer');
    }

    public function send($from, $to, $subject, $body, $cc = null, $bcc = null, $replayto = null, $filespath = null)
    {
        try {
            $mail = \Swift_Message::newInstance()
                ->setTo($to)
                ->setFrom($from)
                ->setCc($cc)
                ->setBcc($bcc)
                ->setReplyTo($replayto)
                ->setSubject($subject)
                ->setBody($body,'text/html');
            if (is_array($filespath)) {
                foreach ($filespath as $file) {
                    $this->message->attach(\Swift_Attachment::fromPath($file));
                }
            }
            $this->mailer->send($mail);
        } catch (\Exception $e) {
            return false;
        }
    }

}