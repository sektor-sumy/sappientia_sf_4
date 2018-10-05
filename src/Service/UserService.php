<?php
namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyRegisteredException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 */
class UserService
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $user
     *
     * @throws UserAlreadyRegisteredException
     */
    public function register(User $user)
    {
        $email = mb_strtolower($user->getEmail());
        $isExists = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($isExists) {
            throw new UserAlreadyRegisteredException();
        }
        if (is_null($user->getPassword())) {
            $password = $this->generateRandomPassword();
            $user->setPassword($password);
        }
        $encoder = $this->passwordEncoder;
        $encodedPassword = $encoder->encodePassword($user, $user->getPassword());
        $user->setEmail($email);
        $user->setPassword($encodedPassword);
        $user->setEmail($email);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function generateRandomPassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }
}
