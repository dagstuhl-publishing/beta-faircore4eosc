<?php

namespace App\Models;

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

    public function getDataCiteRecord(): DataCiteRecord
    {
        $dataProvider = new SwhDepositDataProvider($this);
        $dataCiteRecord = DataCiteRecord::fromDataProvider($dataProvider);
        return $dataCiteRecord;
    }
}
