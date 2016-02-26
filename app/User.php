<?php

namespace App;

use \App\Proposal;
use \App\Like;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'uf'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // User has many Proposals
    public function proposals() {
        return $this->hasMany(Proposal::class);
    }

    // User Likes Proposals
    public function likes() {
        return $this->belongsToMany(Proposal::class, 'likes', 'user_id',  'proposal_id' );
    }

    // User has only one State
    public function states() {
        return $this->hasOne(State::class);
    }
}
