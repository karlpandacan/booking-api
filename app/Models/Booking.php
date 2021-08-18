<?php

/**
 * @author: Karl Pandacan
 * @page: Booking Model
 * @created: 2021-08-18
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'time_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',

    ];

    protected $dates = ['date_from', 'date_to'];

    const PER_PAGES = 15;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeFilterDate($query, $params)
    {
        return $query->whereDate('date_from', '>=', $params->dateFrom)
            ->whereDate('date_to', '<=', $params->dateTo);
    }
    
    public function scopeFilterDateTime($query, $params)
    {
        return $query->where('date_from', '>=', $params->dateFrom)
            ->where('date_to', '<=', $params->dateTo);
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', fn ($query) =>
        $query->where('fullname', 'LIKE', '%' . $search . '%'))
            ->orWhereHas('room', fn ($query) =>
            $query->where('name', 'LIKE', '%' . $search . '%'));
    }
}
