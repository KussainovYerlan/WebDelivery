<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"login"}, message="There is already an account with this login")
 *
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *     message="Please enter your email."
     * )
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     * )
     * @Assert\Length(
     *      max = 180,
     *      maxMessage = "Your email must be shorter than {{ limit }} characters."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     *
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=120, unique=true)
     * @Assert\NotBlank(
     *     message="Please enter your login."
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 120,
     *      minMessage = "Your login must be longer than {{ limit }} characters.",
     *      maxMessage = "Your login must be shorter than {{ limit }} characters."
     * )
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seller", inversedBy="users")
     */
    private $seller;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DeliveryOrder", mappedBy="user", orphanRemoval=true)
     */
    private $deliveryOrders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->deliveryOrders = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->login;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @return Collection|DeliveryOrder[]
     */
    public function getDeliveryOrders(): Collection
    {
        return $this->deliveryOrders;
    }

    public function addDeliveryOrder(DeliveryOrder $deliveryOrder): self
    {
        if (!$this->deliveryOrders->contains($deliveryOrder)) {
            $this->deliveryOrders[] = $deliveryOrder;
            $deliveryOrder->setUser($this);
        }

        return $this;
    }

    public function removeDeliveryOrder(DeliveryOrder $deliveryOrder): self
    {
        if ($this->deliveryOrders->contains($deliveryOrder)) {
            $this->deliveryOrders->removeElement($deliveryOrder);
            // set the owning side to null (unless already changed)
            if ($deliveryOrder->getUser() === $this) {
                $deliveryOrder->setUser(null);
            }
        }

        return $this;
    }
}
