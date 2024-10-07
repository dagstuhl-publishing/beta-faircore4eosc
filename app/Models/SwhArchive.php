<?php

namespace App\Models;

use App\Modules\Utils;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveRequestStatus;
use Dagstuhl\SwhArchiveClient\SwhObjects\SaveTaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwhArchive extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "saveRequestStatus" => SaveRequestStatus::class,
            "saveTaskStatus" => SaveTaskStatus::class,
            'requested_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

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
