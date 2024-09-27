<?php

namespace App\Modules\CodeMeta;

class CodeMetaAffiliation
{
    public ?string $name = null;

    public array $_other = [];

    public static function fromJson(string|object $json): CodeMetaAffiliation
    {
        if(is_string($json)) {
            $json = json_decode($json);
        }

        $affiliation = new CodeMetaAffiliation();
        $affiliation->name = $json->name ?? null;

        //TODO $_other

        return $affiliation;
    }
}
