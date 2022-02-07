<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PostRepository;
use App\Utils\UserOwenedInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    paginationClientItemsPerPage: true,
paginationMaximumItemsPerPage: 24,
paginationItemsPerPage: 24,
normalizationContext:['groups'=>['read:collection']],
    denormalizationContext:['groups'=>['write:Post']],
collectionOperations:[
    'get'=>[

        'openapi_context'=> [
            'security'=>[['bearerAuth'=>[]]]
        ]
    ],
    'post'=>[
        'validation_groups'=>[Post::class,'validationGroups']
    ]
    ],
itemOperations: [
    'put',
    'delete',
    'get'=>[
        'openapi_context'=> [
            'security'=>[['bearerAuth'=>[]]]
        ],
    'normalization_context'=>['groups'=>['read:collection','read:item','write:post']]
    ]]
),
ApiFilter(  SearchFilter::class,properties: ['id'=>'exact','title'=>'partial'])]
class Post implements UserOwenedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read:collection')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:collection','write:Post']),
        Length(min: 5,groups: ['create:post'])
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(
        ['read:collection','write:Post'])
    ]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:item','write:Post'])]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:item'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:item','write:Post'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[Groups(['read:item','write:Post'])]
    private $category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private $user;


    /**
     * method statique pour gerer la validation
     * @param Post $post
     * @return string[]
     */
    public static function validationGroups(self $post){
        return ['create:post'];
    }
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

 
}
