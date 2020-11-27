<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="This email address: {{ value }} already exist !", groups={"Create"})
 * @UniqueEntity(fields={"username"}, message="This username: {{ value }} already exist !", groups={"Create"})
 *  
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_users_show",
 *          parameters = { "customerId" = "expr(object.getCustomer().getId())", "userId" = "expr(object.getId())", },
 *          absolute = true        
 *      )      
 * )
 * 
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "api_customers_users_list",
 *          parameters = { "id" = "expr(object.getCustomer().getId())"},
 *          absolute = true        
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"userList"})
 * )
 * 
 *  @Hateoas\Relation(
 *     "customer",
 *     embedded = @Hateoas\Embedded("expr(object.getCustomer())")
 * )
 * 
 * @Serializer\ExclusionPolicy("ALL")
 * 
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(groups={"Create"}) 
     * @Serializer\Expose
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(groups={"Create"}) 
     * @Serializer\Expose 
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(groups={"Create"})
     * @Serializer\Expose 
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Create"})
     * @Assert\Email(groups={"Create"})
     * @Serializer\Expose 
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
