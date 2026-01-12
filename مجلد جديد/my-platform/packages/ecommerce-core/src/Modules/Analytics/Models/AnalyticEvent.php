<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AnalyticEvent extends Model
{
    protected $table = 'analytics_events';
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'user_id',
        'event_name',
        'event_data',
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

// Separate file would be better, but implementing both here for speed if preferred. 
// Actually I'll create a separate file for Conversion.
