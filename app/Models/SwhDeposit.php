<?php

namespace App\Models;

use App\Modules\CodeMeta\CodeMetaRecord;
use App\Modules\SwhDepositDataProvider;
use App\Modules\Utils;
use Dagstuhl\DataCite\Metadata\DataCiteRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwhDeposit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDoi(): string
    {
        return "10.0000/deposit-".$this->uuid;
    }

    public function getUrl(): string
    {
        return route("swh-deposits.show", [ "deposit" => $this ]);
    }

    public function getFormattedArchiveSize(): string
    {
        return Utils::formatFileSize($this->archiveSize);
    }

    public function getBrowseUrl(): string
    {
        if($this->depositSwhIdContext === null) {
            return "#";
        }
        return "https://webapp.staging.swh.network/{$this->depositSwhIdContext}";
    }

    public function getCodeMetaRecord(): CodeMetaRecord
    {
        return CodeMetaRecord::fromJson($this->codemetaJson);
    }

    public function exportDataCiteRecord(): DataCiteRecord
    {
        $dataProvider = new SwhDepositDataProvider($this);
        $dataCiteRecord = DataCiteRecord::fromDataProvider($dataProvider);
        return $dataCiteRecord;
    }
}
