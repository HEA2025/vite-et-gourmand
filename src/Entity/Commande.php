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

    #[ORM\Column(nullable: true)]
    private ?float $distanceLivraison = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixTotal = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    // Indique si le client doit restituer du matériel après la prestation.
    #[ORM\Column(options: ['default' => false])]
    private bool $materielPrete = false;

    // Conserve la manière dont l'employé a contacté le client.
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $moyenContactEmploye = null;

    // Conserve la raison d'une modification ou annulation faite par l'employé.
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motifInterventionEmploye = null;

    // Permet de savoir quand le client a été contacté.
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateContactEmploye = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    /**
     * @var Collection<int, HistoriqueStatutCommande>
     */
    #[ORM\OneToMany(
        targetEntity: HistoriqueStatutCommande::class,
        mappedBy: 'commande'
    )]
    private Collection $historiqueStatutCommandes;

    #[ORM\OneToOne(mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Avis $avis = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

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

    public function getDistanceLivraison(): ?float
    {
        return $this->distanceLivraison;
    }

    public function setDistanceLivraison(?float $distanceLivraison): static
    {
        $this->distanceLivraison = $distanceLivraison;

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

    public function isMaterielPrete(): bool
    {
        return $this->materielPrete;
    }

    public function setMaterielPrete(bool $materielPrete): static
    {
        $this->materielPrete = $materielPrete;

        return $this;
    }

    public function getMoyenContactEmploye(): ?string
    {
        return $this->moyenContactEmploye;
    }

    public function setMoyenContactEmploye(?string $moyenContactEmploye): static
    {
        $this->moyenContactEmploye = $moyenContactEmploye;

        return $this;
    }

    public function getMotifInterventionEmploye(): ?string
    {
        return $this->motifInterventionEmploye;
    }

    public function setMotifInterventionEmploye(
        ?string $motifInterventionEmploye
    ): static {
        $this->motifInterventionEmploye = $motifInterventionEmploye;

        return $this;
    }

    public function getDateContactEmploye(): ?\DateTimeImmutable
    {
        return $this->dateContactEmploye;
    }

    public function setDateContactEmploye(
        ?\DateTimeImmutable $dateContactEmploye
    ): static {
        $this->dateContactEmploye = $dateContactEmploye;

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

    public function addHistoriqueStatutCommande(
        HistoriqueStatutCommande $historiqueStatutCommande
    ): static {
        if (
            !$this->historiqueStatutCommandes
                ->contains($historiqueStatutCommande)
        ) {
            $this->historiqueStatutCommandes
                ->add($historiqueStatutCommande);

            $historiqueStatutCommande->setCommande($this);
        }

        return $this;
    }

    public function removeHistoriqueStatutCommande(
        HistoriqueStatutCommande $historiqueStatutCommande
    ): static {
        if (
            $this->historiqueStatutCommandes
                ->removeElement($historiqueStatutCommande)
        ) {
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
        if ($avis->getCommande() !== $this) {
            $avis->setCommande($this);
        }

        $this->avis = $avis;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}