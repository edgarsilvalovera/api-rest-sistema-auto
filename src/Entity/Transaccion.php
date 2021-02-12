<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaccion
 *
 * @ORM\Table(name="transacciones", indexes={@ORM\Index(name="fk_autos", columns={"auto_id"}), @ORM\Index(name="fk_servicios", columns={"servicio_id"})})
 * @ORM\Entity
 */
class Transaccion 
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
     * @var float
     *
     * @ORM\Column(name="costo_servicio_transaccion", type="float", precision=10, scale=0, nullable=false)
     */
    private $costoServicioTransaccion;

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

    // /**
    //  * @var \Autos
    //  *
    //  * @ORM\ManyToOne(targetEntity="Auto")
    //  * @ORM\JoinColumns({
    //  *   @ORM\JoinColumn(name="auto_id", referencedColumnName="id")
    //  * })
    //  */
    // private $auto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Auto", inversedBy="transacciones")
     */
    private $auto;


    /**
     * @var \Servicios
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="servicio_id", referencedColumnName="id")
     * })
     */
    private $servicio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCostoServicioTransaccion(): ?float
    {
        return $this->costoServicioTransaccion;
    }

    public function setCostoServicioTransaccion(float $costoServicioTransaccion): self
    {
        $this->costoServicioTransaccion = $costoServicioTransaccion;

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

    public function getAuto(): ?Auto
    {
        return $this->auto;
    }

    public function setAuto(?Auto $auto): self
    {
        $this->auto = $auto;

        return $this;
    }

    public function getServicio(): ?Servicio
    {
        return $this->servicio;
    }

    public function setServicio(?Servicio $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }


}
