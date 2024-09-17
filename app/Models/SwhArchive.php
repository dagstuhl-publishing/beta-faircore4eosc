<?php

namespace App\Models;

use App\Modules\Utils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwhArchive extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBrowseUrl(): string
    {
        if($this->swhIdContext === null) {
            return "#";
        }
        return "https://webapp.staging.swh.network/{$this->swhIdContext}";
    }
}
