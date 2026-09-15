<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adressePrestation = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $datePrestation = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $heureLivraison = null;

    #[ORM\Column]
    private ?int $nombrePersonnes = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixMenu = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $reduction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $fraisLivraison = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixTotal = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    /**
     * @var Collection<int, HistoriqueStatutCommande>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueStatutCommande::class, mappedBy: 'commande')]
    private Collection $historiqueStatutCommandes;

    #[ORM\OneToOne(mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Avis $avis = null;

    public function __construct()
    {
        $this->historiqueStatutCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdressePrestation(): ?string
    {
        return $this->adressePrestation;
    }

    public function setAdressePrestation(string $adressePrestation): static
    {
        $this->adressePrestation = $adressePrestation;

        return $this;
    }

    public function getDatePrestation(): ?\DateTimeImmutable
    {
        return $this->datePrestation;
    }

    public function setDatePrestation(\DateTimeImmutable $datePrestation): static
    {
        $this->datePrestation = $datePrestation;

        return $this;
    }

    public function getHeureLivraison(): ?\DateTimeImmutable
    {
        return $this->heureLivraison;
    }

    public function setHeureLivraison(\DateTimeImmutable $heureLivraison): static
    {
        $this->heureLivraison = $heureLivraison;

        return $this;
    }

    public function getNombrePersonnes(): ?int
    {
        return $this->nombrePersonnes;
    }

    public function setNombrePersonnes(int $nombrePersonnes): static
    {
        $this->nombrePersonnes = $nombrePersonnes;

        return $this;
    }

    public function getPrixMenu(): ?string
    {
        return $this->prixMenu;
    }

    public function setPrixMenu(string $prixMenu): static
    {
        $this->prixMenu = $prixMenu;

        return $this;
    }

    public function getReduction(): ?string
    {
        return $this->reduction;
    }

    public function setReduction(string $reduction): static
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getFraisLivraison(): ?string
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(string $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;

        return $this;
    }

    public function getPrixTotal(): ?string
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(string $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueStatutCommande>
     */
    public function getHistoriqueStatutCommandes(): Collection
    {
        return $this->historiqueStatutCommandes;
    }

    public function addHistoriqueStatutCommande(HistoriqueStatutCommande $historiqueStatutCommande): static
    {
        if (!$this->historiqueStatutCommandes->contains($historiqueStatutCommande)) {
            $this->historiqueStatutCommandes->add($historiqueStatutCommande);
            $historiqueStatutCommande->setCommande($this);
        }

        return $this;
    }

    public function removeHistoriqueStatutCommande(HistoriqueStatutCommande $historiqueStatutCommande): static
    {
        if ($this->historiqueStatutCommandes->removeElement($historiqueStatutCommande)) {
            // set the owning side to null (unless already changed)
            if ($historiqueStatutCommande->getCommande() === $this) {
                $historiqueStatutCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getAvis(): ?Avis
    {
        return $this->avis;
    }

    public function setAvis(Avis $avis): static
    {
        // set the owning side of the relation if necessary
        if ($avis->getCommande() !== $this) {
            $avis->setCommande($this);
        }

        $this->avis = $avis;

        return $this;
    }
}
