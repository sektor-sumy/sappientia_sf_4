<?php
/**
 * Created by PhpStorm.
 * User: mihailbogdanov
 * Date: 05.10.2018
 * Time: 13:41
 */
namespace App\Security\User;

use App\Entity\User;
use App\Exception\UserAuthException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * Class WebserviceUserProvider
 */
class WebserviceUserProvider implements UserProviderInterface
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function getEm()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        $userRepo = $this->getEm()->getRepository(User::class);

        return $userRepo->getClassName() === $class || is_subclass_of($class, $userRepo->getClassName());
    }

    /**
     * @param UserInterface $user
     *
     * @return User
     *
     * @throws UserAuthException
     */
    public function refreshUser(UserInterface $user)
    {
        /* @var User $user */
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $class)
            );
        }

        try {
            $user = $this->getEm()->getRepository(User::class)->find($user->getId());
        } catch (\Exception $e) {
            throw new UserAuthException('Unexpected error occurred.');
        }

        return $user;
    }

    /**
     * @param string $username
     *
     * @return User
     *
     * @throws \Exception
     */
    public function loadUserByUsername($username)
    {
        try {
            $user = $this->getEm()->getRepository(User::class)->findOneBy([
                'email' => strtolower($username),
            ]);
        } catch (\Exception $e) {
            throw new UserAuthException('Unexpected error occurred.');
        }

        if (!$user) {
            $message = sprintf('Unable to find an active user identified by "%s".', $username);
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }
}