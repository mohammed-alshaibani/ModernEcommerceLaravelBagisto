<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Visit extends Model
{
    protected $table = 'analytics_visits';
    public $timestamps = false; // Using created_at only

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'url',
        'referer',
        'user_agent',
        'device_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
