<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Eloquent
{
    use SoftDeletes;

    //public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'pub_date', 'limit_date'];

    protected $fillable = ['name', 'idea_central', 'problem', 'idea_exposition', 'user_id', 'approved', 'pub_date', 'limit_date'];

    //protected $guarded = ['id', 'pub_date', 'limit_date'];

    // Proposal __belongs_to__ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Approvals __belongs_to_many__ Proposal
    public function approvals()
    {
        return $this->belongsToMany(User::class, 'approvals', 'proposal_id', 'user_id');
    }

    // Proposal __belongs_to__ Responder (User)
    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // Proposal __has_many__ Likes
    /*public function likes()
    {
        return $this->hasMany(Like::class);
    }*/

    // Proposal __has_one_Bill_Project
    public function bill_project()
    {
        return $this->hasOne(BillsProject::class, 'id', 'bill_project_id');
    }

    // Rating = Lower bound of Wilson score confidence interval for a Bernoulli parameter
    /*public function getRatingAttribute()
    {
        $like = $this->like_count;
        //$like = Like::where('proposal_id', $this->id)->where('like', 1)->count();
        $unlike = $this->unlike_count;
        //$unlike = Like::where('proposal_id', $this->id)->where('like', 0)->count();

        if ($like == 0 && $unlike == 0) {
            return 0;
        }

        return (($like + 1.9208) / ($like + $unlike) -
                1.96 * sqrt(($like * $unlike) / ($like + $unlike) + 0.9604) /
                ($like + $unlike)) / (1 + 3.8416 / ($like + $unlike));
    }*/

    public function getLikeCountAttribute()
    {
        return Like::where('proposal_id', $this->id)->where('like', 1)->count();
    }

    public function getUnlikeCountAttribute()
    {
        return Like::where('proposal_id', $this->id)->where('like', 0)->count();
    }

    /*public function getTotalLikeCountAttribute()
    {
        return $this->like_count - $this->unlike_count;
    }*/

   /* public function getApprovalsCountAttribute()
    {
        return User::all()->approvals()->get()->count();
    }*/
}
