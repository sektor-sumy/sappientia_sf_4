<?php


namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Class EmailService
 */
class EmailService
{
    /**
     * @var string
     */
    private $mailer;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * EmailService constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param TwigEngine    $templating
     */
    public function __construct(\Swift_Mailer $mailer, TwigEngine $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param User   $user
     * @param string $message
     *
     * @throws \Twig\Error\Error
     */
    public function sendCustomEmail(User $user, $message)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(':Email/custom-message:email.html.twig', [
                    'user' => $user,
                    'message' => $message,
                ]),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
