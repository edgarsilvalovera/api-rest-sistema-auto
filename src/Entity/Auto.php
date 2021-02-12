<?php

namespace App\Entity;

use App\Repository\AutoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Auto
 *
 * @ORM\Table(name="autos", indexes={@ORM\Index(name="fk_propietarios", columns={"propietario_id"})})
 * @ORM\Entity(repositoryClass=AutoRepository::class)
 */
class Auto implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="marca", type="string", length=50, nullable=false)
     */
    private $marca;

    /**
     * @var string
     *
     * @ORM\Column(name="modelo", type="string", length=50, nullable=false)
     */
    private $modelo;

    /**
     * @var int
     *
     * @ORM\Column(name="anio", type="integer", nullable=false)
     */
    private $anio;

    /**
     * @var string
     *
     * @ORM\Column(name="patente", type="string", length=50, nullable=false)
     */
    private $patente;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=50, nullable=false)
     */
    private $color;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="current_timestamp()"})
     */
    private $createdAt = 'current_timestamp()';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="current_timestamp()"})
     */
    private $updatedAt = 'current_timestamp()';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Propietario", inversedBy="autos")
     */
    private $propietario;

    public function getPropietario(): ?Propietario
    {
        return $this->propietario;
    }

    public function setPropietario(?Propietario $propietario): self
    {
        $this->propietario = $propietario;

        return $this;
    }

    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaccion", mappedBy="auto")
     */
    private $transacciones;

    public function __construct()
    {
        $this->transacciones = new ArrayCollection();
    }

    /**
     * @return Collection|Transaccion[]
     */
    public function getTransacciones(): Collection
    {
        return $this->transacciones;
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getAnio(): ?int
    {
        return $this->anio;
    }

    public function setAnio(int $anio): self
    {
        $this->anio = $anio;

        return $this;
    }

    public function getPatente(): ?string
    {
        return $this->patente;
    }

    public function setPatente(string $patente): self
    {
        $this->patente = $patente;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio' => $this->anio,
            'patente' => $this->patente,
            'color' => $this->color ,
            'propietario' => $this->propietario
        ];
    }


}
