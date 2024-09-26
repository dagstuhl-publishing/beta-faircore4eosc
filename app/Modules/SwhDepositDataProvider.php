<?php

namespace App\Modules;

use App\Models\SwhDeposit;
use Carbon\CarbonImmutable;
use Dagstuhl\DataCite\DataCiteDataProvider;
use Dagstuhl\DataCite\Metadata\AlternateIdentifier;
use Dagstuhl\DataCite\Metadata\Contributor;
use Dagstuhl\DataCite\Metadata\Creator;
use Dagstuhl\DataCite\Metadata\Date;
use Dagstuhl\DataCite\Metadata\Description;
use Dagstuhl\DataCite\Metadata\NameIdentifier;
use Dagstuhl\DataCite\Metadata\RelatedIdentifier;
use Dagstuhl\DataCite\Metadata\RelatedItem;
use Dagstuhl\DataCite\Metadata\Rights;
use Dagstuhl\DataCite\Metadata\Subject;
use Dagstuhl\DataCite\Metadata\Title;
use Dagstuhl\DataCite\Metadata\Type;

class SwhDepositDataProvider implements DataCiteDataProvider
{
    const PUBLISHER = "Schloss Dagstuhl – Leibniz-Zentrum für Informatik";

    private SwhDeposit $swhDeposit;
    private object $codemetaJson;

    public function __construct(SwhDeposit $swhDeposit)
    {
        $this->swhDeposit = $swhDeposit;
        $this->codemetaJson = json_decode($swhDeposit->codemetaJson);
    }

    public function getDoi(): string
    {
        return "10.0000/deposits/".$this->swhDeposit->uuid;
    }

    public function getUrl(): string
    {
        return route("swh-deposits.show", [ "deposit" => $this->swhDeposit ]);
    }

    public function getPublisher(): string
    {
        return static::PUBLISHER;
    }

    /**
     * @return Title[]
     */
    public function getTitles(): array
    {
        return [ new Title($this->codemetaJson->name) ];
    }

    /**
     * @return Creator[]
     */
    public function getCreators(): array
    {
        $authors = $this->codemetaJson->author ?? [];
        if(!is_array($authors)) {
            $authors = [ $authors ];
        }

        return array_map(
            function($author) {
                $name = $author->name ?? "{$author->givenName} {$author->familyName}";
                $creator = Creator::personal($name, $author->givenName, $author->familyName);

                if(preg_match('/^https:\\/\\/orcid.org\\/(.*)$/', $author->{"@id"}, $matches)) {
                    $creator->addNameIdentifier(NameIdentifier::orcid($author->{"@id"}));
                }

                if(isset($author->affiliation)) {
                    $creator->addAffiliation(new Affiliation($author->affiliation->name));
                }

                return $creator;
            },
            $authors,
        );
    }

    /**
     * @return Contributor[]
     */
    public function getContributors(): array
    {
        return [];
    }

    /**
     * @return Description[]
     */
    public function getDescriptions(): array
    {
        if(isset($this->codemetaJson->description)) {
            //TODO abstract?
            return [ Description::abstract($this->codemetaJson->description) ];
        } else {
            return [];
        }
    }

    /**
     * @return string[]
     */
    public function getSizes(): array
    {
        if($this->swhDeposit->archiveSize !== null) {
            return [ "{$this->swhDeposit->archiveSize} B" ];
        } else {
            return [];
        }
    }

    /**
     * @return string[]
     */
    public function getFormats(): array
    {
        if($this->swhDeposit->archiveContentType !== null) {
            return [ $this->swhDeposit->archiveContentType ];
        } else {
            return [];
        }
    }

    public function getPublicationYear(): int
    {
        if(isset($this->codemetaJson->dateCreated)) {
            return CarbonImmutable::parse($this->codemetaJson->dateCreated)->year;
        } else {
            return $this->swhDeposit->created_at->year;
        }
    }

    /**
     * @return Subject[]
     */
    public function getSubjects(): array
    {
        $keywords = $this->codemetaJson->keywords ?? [];
        if(!is_array($keywords)) {
            $keywords = [ $keywords ];
        }

        return array_map(fn($keyword) => new Subject($keyword), $keywords);
    }

    public function getLanguage(): string
    {
        return "en";
    }

    /**
     * @return AlternateIdentifier[]
     */
    public function getAlternateIdentifiers(): array
    {
        //TODO return swhId here?
        return [];
    }

    /**
     * @return RelatedIdentifier[]
     */
    public function getRelatedIdentifiers(): array
    {
        return [];
    }

    /**
     * @return RelatedItem[]
     */
    public function getRelatedItems(): array
    {
        return [];
    }

    /**
     * @return Rights[]
     */
    public function getRightsList(): array
    {
        $licenses = $this->codemetaJson->license ?? [];
        if(!is_array($licenses)) {
            $licenses = [ $licenses ];
        }

        $rights = [];
        foreach($licenses as $license) {
            if(preg_match('/^https?:\\/\\/spdx.org\\/licenses\\/(.*)$/', $license, $matches)) {
                $rights[] = new Rights($matches[1], $license, "en", Rights::RIGHTS_IDENTIFIER_SCHEME_SPDX, Rights::SCHEME_URI_SPDX);
            }
        }
        return $rights;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return new Type(Type::RESOURCE_TYPE_GENERAL_SOFTWARE);
    }

    /**
     * @return Date[]
     */
    public function getDates(): array
    {
        $createdAt = $this->swhDeposit->created_at->format("Y-m-d");
        return [
            Date::created($this->codemetaJson->dateCreated ?? $createdAt),
            Date::available($this->codemetaJson->datePublished ?? $createdAt),
            Date::issued($createdAt),
            Date::submitted($createdAt),
            Date::accepted($createdAt),
            Date::copyrighted($createdAt),
        ];
    }
}
