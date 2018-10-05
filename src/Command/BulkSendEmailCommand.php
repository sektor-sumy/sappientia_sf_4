<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BulkSendEmailCommand
 */
class BulkSendEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cariba:send-email')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Pleas enter email!')
            ->addOption(
                'message',
                null,
                InputOption::VALUE_OPTIONAL,
                'Pleas enter message?',
                'Default message'
            )
            ->setDescription('Send email.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getOption('email');
        $message = $input->getOption('message');

        $output->write('Start email notification.');
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        if ($user) {
            $this->getContainer()->get('email.service')->sendCustomEmail($user, $message);
            $output->write('Finish.');
        } else {
            $output->write('User not found.');
        }
    }
}
