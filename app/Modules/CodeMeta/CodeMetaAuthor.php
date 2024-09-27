<?php

namespace App\Modules\CodeMeta;

class CodeMetaAuthor
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $givenName = null;
    public ?string $familyName = null;
    public ?string $email = null;
    public ?array $affiliations = [];

    public array $_other = [];

    public static function fromJson(string|object $json): CodeMetaAuthor
    {
        if(is_string($json)) {
            $json = json_decode($json);
        }

        $author = new CodeMetaAuthor();
        $author->id = $json->{"@id"} ?? null;
        $author->name = $json->name ?? null;
        $author->givenName = $json->givenName ?? null;
        $author->familyName = $json->familyName ?? null;
        $author->email = $json->email ?? null;

        $affiliations = $json->affiliation ?? [];
        if(!is_array($affiliations)) {
            $affiliations = [ $affiliations ];
        }
        $author->affiliations = array_map(fn($affiliation) => CodeMetaAffiliation::fromJson($affiliation), $affiliations);

        //TODO $_other

        return $author;
    }
}
