<?php

namespace App\Modules\CodeMeta;

class CodeMetaRecord
{
    public ?string $name = null;
    public array $authors = [];
    public ?string $description = null;
    public ?string $version = null;
    public ?string $dateCreated = null;
    public ?string $datePublished = null;
    public array $licenses = [];
    public array $keywords = [];
    public array $programmingLanguages = [];
    public ?string $developmentStatus = null;

    public array $_other = [];

    public static function fromJson(string|object $json): CodeMetaRecord
    {
        if(is_string($json)) {
            $json = json_decode($json);
        }

        $record = new CodeMetaRecord();
        $record->name = $json->name ?? null;
        $record->description = $json->description ?? null;
        $record->version = $json->version ?? null;
        $record->dateCreated = $json->dateCreated ?? null;
        $record->datePublished = $json->datePublished ?? null;
        $record->developmentStatus = $json->developmentStatus ?? null;

        $authors = $json->author ?? [];
        if(!is_array($authors)) {
            $authors = [ $authors ];
        }
        $record->authors = array_map(fn($author) => CodeMetaAuthor::fromJson($author), $authors);

        $licenses = $json->license ?? [];
        if(!is_array($licenses)) {
            $licenses = [ $licenses ];
        }
        $record->licenses = $licenses;

        $keywords = $json->keywords ?? [];
        if(!is_array($keywords)) {
            $keywords = [ $keywords ];
        }
        $record->keywords = $keywords;

        $programmingLanguages = $json->programmingLanguage ?? [];
        if(!is_array($programmingLanguages)) {
            $programmingLanguages = [ $programmingLanguages ];
        }
        $record->programmingLanguages = $programmingLanguages;

        //TODO $_other

        return $record;
    }
}
