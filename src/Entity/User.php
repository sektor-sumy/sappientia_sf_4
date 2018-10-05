<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    const SECRET_KEY = 'sdfjff4DfR674XdsfVN';

    const ROLE_ROOT = 'ROLE_ROOT';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';

    const STATUS_BANNED = 'banned';
    const STATUS_NOT_CONFIRMED = 'not-confirmed';
    const STATUS_ACTIVE = 'active';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     */
    private $salt;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

    /**
     * @ORM\Column(name="email_confirmed_at", type="datetime", nullable=true)
     */
    private $emailConfirmedAt;

    /**
     * @ORM\Column(name="banned_at", type="datetime", nullable=true)
     */
    private $bannedAt;

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255)
     */
    private $hash;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registeredAt = new \DateTime();
        $this->salt = hash('sha256', uniqid(null, true));
        $this->role = self::ROLE_USER;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [$this->role];
    }
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = mb_strtolower($email);

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return User
     */
    public function setBannedAt($dateTime)
    {
        $this->bannedAt = $dateTime;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBannedAt()
    {
        return $this->bannedAt;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return !$this->getBannedAt();
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param \DateTime $registeredAt
     *
     * @return User
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return User
     */
    public function setEmailConfirmedAt($dateTime)
    {
        $this->emailConfirmedAt = $dateTime;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEmailConfirmedAt()
    {
        return $this->emailConfirmedAt;
    }

    /**
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        if ($this->getBannedAt()) {
            return self::STATUS_BANNED;
        }
        if (!$this->getEmailConfirmedAt()) {
            return self::STATUS_NOT_CONFIRMED;
        }

        return self::STATUS_ACTIVE;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }


    /**
     * @param string $hash
     *
     * @return string
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function generateHash()
    {
        $now = new \DateTime();
        $data = [
            'now', $now->getTimestamp(),
            'secretKey', self::SECRET_KEY,
            'email', $this->getEmail(),
            'username', $this->getUsername(),
        ];
        $hash = hash('sha256', implode('', $data));

        return $hash;
    }
}
