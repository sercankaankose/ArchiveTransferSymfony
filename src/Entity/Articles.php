<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Journal $journal = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Issues $issue = null;

    #[ORM\Column(length: 20)]
    private ?string $primary_language = null;

    #[ORM\Column(length: 255)]
    private ?string $fulltext = null;

    #[ORM\Column(nullable: true)]
    private ?int $first_page = null;

    #[ORM\Column(nullable: true)]
    private ?int $last_page = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $doi = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Translations::class)]
    private Collection $translations;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Authors::class)]
    private Collection $authors;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Citations::class)]
    private Collection $citations;

    #[ORM\Column(nullable: true)]
    private ?array $errors = null;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->citations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJournal(): ?Journal
    {
        return $this->journal;
    }

    public function setJournal(?Journal $journal): static
    {
        $this->journal = $journal;

        return $this;
    }

    public function getIssue(): ?Issues
    {
        return $this->issue;
    }

    public function setIssue(?Issues $issue): static
    {
        $this->issue = $issue;

        return $this;
    }

    public function getPrimaryLanguage(): ?string
    {
        return $this->primary_language;
    }

    public function setPrimaryLanguage(string $primary_language): static
    {
        $this->primary_language = $primary_language;

        return $this;
    }

    public function getFulltext(): ?string
    {
        return $this->fulltext;
    }

    public function setFulltext(string $fulltext): static
    {
        $this->fulltext = $fulltext;

        return $this;
    }

    public function getFirstPage(): ?int
    {
        return $this->first_page;
    }

    public function setFirstPage(?int $first_page): static
    {
        $this->first_page = $first_page;

        return $this;
    }

    public function getLastPage(): ?int
    {
        return $this->last_page;
    }

    public function setLastPage(?int $last_page): static
    {
        $this->last_page = $last_page;

        return $this;
    }

    public function getDoi(): ?string
    {
        return $this->doi;
    }

    public function setDoi(?string $doi): static
    {
        $this->doi = $doi;

        return $this;
    }

    /**
     * @return Collection<int, Translations>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(Translations $translation): static
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setArticle($this);
        }

        return $this;
    }

    public function removeTranslation(Translations $translation): static
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getArticle() === $this) {
                $translation->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Authors>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Authors $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->setArticle($this);
        }

        return $this;
    }

    public function removeAuthor(Authors $author): static
    {
        if ($this->authors->removeElement($author)) {
            // set the owning side to null (unless already changed)
            if ($author->getArticle() === $this) {
                $author->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Citations>
     */
    public function getCitations(): Collection
    {
        return $this->citations;
    }

    public function addCitation(Citations $citation): static
    {
        if (!$this->citations->contains($citation)) {
            $this->citations->add($citation);
            $citation->setArticle($this);
        }

        return $this;
    }

    public function removeCitation(Citations $citation): static
    {
        if ($this->citations->removeElement($citation)) {
            // set the owning side to null (unless already changed)
            if ($citation->getArticle() === $this) {
                $citation->setArticle(null);
            }
        }

        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors(?array $errors): static
    {
        $this->errors = $errors;

        return $this;
    }

}
