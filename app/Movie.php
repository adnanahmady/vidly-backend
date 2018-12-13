<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Movie extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'number_in_stock',
        'daily_rental_rate',
        'genre_id',
        'user_id',
        'publish_data',
        'like',
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
        'publish_date'
    ];

    public function genre() {
        return $this->belongsTo('App\Genre', 'genre_id', 'id');
    }

    public function user() {
        return $this->belognsTo('App\User', 'user_id', 'id');
    }
}
