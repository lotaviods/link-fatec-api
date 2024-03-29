<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

#[Entity()]
class CompanyAddress
{
    #[Id]
    #[GeneratedValue(strategy: "IDENTITY")]
    #[Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[Column(name: "street", type: "string", length: 255, nullable: false)]
    private string $street;

    #[Column(name: "number", type: "string", length: 255, nullable: false)]
    private string $number;

    #[Column(name: "neighborhood", type: "string", length: 255, nullable: false)]
    private string $neighborhood;

    #[Column(name: "complement", type: "string", length: 255, nullable: true)]
    private ?string $complement;

    #[Column(name: "zip_code", type: "string", length: 255, nullable: false)]
    private string $zipCode;

    #[Column(name: "city", type: "string", length: 255, nullable: false)]
    private string $city;

    #[Column(name: "state", type: "string", length: 255, nullable: false)]
    private string $state;

    #[Column(name: "country", type: "string", length: 255, nullable: true, options: ["fixed" => true])]
    private ?string $country;

    #[Column(name: "latitude", type: "float", nullable: true)]
    private ?string $latitude = null;

    #[Column(name: "longitude", type: "float", nullable: true)]
    private ?string $longitude = null;

    #[OneToOne(cascade: ['persist', 'remove'])]
    #[JoinColumn(onDelete: "CASCADE")]
    private ?Company $company = null;

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    /**
     * @param string $neighborhood
     */
    public function setNeighborhood(string $neighborhood): void
    {
        $this->neighborhood = $neighborhood;
    }

    /**
     * @return string|null
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * @param string|null $complement
     */
    public function setComplement(?string $complement): void
    {
        $this->complement = $complement;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'number' => $this->number,
            'neighborhood' => $this->neighborhood,
            'complement' => $this->complement,
            'zip_code'=> $this->zipCode,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }
}
