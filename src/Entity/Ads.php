<?php

namespace App\Entity;

use App\Repository\AdsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdsRepository::class)]
class Ads
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    private $city;

    #[ORM\Column(type: 'string', length: 255)]
    private $text;

    #[ORM\Column(type: 'boolean',nullable: true)]
    private $done;


    #[ORM\ManyToOne(targetEntity: MaterialType::class, inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    private $materialType;

    #[ORM\ManyToOne(targetEntity: AdType::class, inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    private $adType;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private ?Media $media;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'ads')]
    private $brand;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userAds')]
    #[ORM\JoinColumn(nullable: true)]
    private $author;


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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }



    public function getMaterialType(): ?MaterialType
    {
        return $this->materialType;
    }

    public function setMaterialType(?MaterialType $materialType): self
    {
        $this->materialType = $materialType;

        return $this;
    }

    public function getAdType(): ?AdType
    {
        return $this->adType;
    }

    public function setAdType(?AdType $adType): self
    {
        $this->adType = $adType;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }




}
