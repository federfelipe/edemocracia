<?php

namespace App;

use App\Like;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Proposal extends Eloquent {

    //public $timestamps = false;

    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'pub_date', 'limit_date'];

    protected $fillable = ['name', 'idea_central', 'problem', 'idea_exposition', 'user_id', 'situation', 'pub_date', 'limit_date'];

    //protected $guarded = ['id', 'pub_date', 'limit_date'];

    // Proposal __belongs_to__ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Approvals __belongs_to_many__ Proposal
    public function approvals()
    {
        return $this->belongsToMany(User::class, 'approvals', 'proposal_id', 'user_id' );
    }

    // Proposal __belongs_to__ Responder (User)
    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    // Proposal __has_many__ Likes
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Rating = Lower bound of Wilson score confidence interval for a Bernoulli parameter
    public function getRatingAttribute()
    {
//        $like = getLikeCountAttribute();
      $like = Like::where('proposal_id', $this->id)->where('like', 1)->count();
//        $unlike = getUnlikeCountAttribute();
      $unlike = Like::where('proposal_id', $this->id)->where('like', 0)->count();

        if ($like == 0 && $unlike == 0) {
            return 0;
        }

        return (($like + 1.9208) / ($like + $unlike) -
                1.96 * SQRT(($like * $unlike) / ($like + $unlike) + 0.9604) /
                ($like + $unlike)) / (1 + 3.8416 / ($like + $unlike));
    }

    public function getLikeCountAttribute()
    {
        return Like::where('proposal_id', $this->id)->where('like', 1)->count();
    }

    public function getUnlikeCountAttribute()
    {
        return Like::where('proposal_id', $this->id)->where('like', 0)->count();
    }

    public function getTotalLikeCountAttribute()
    {
        return (getLikeCountAttribute() - getUnlikeCountAttribute());
    }

    public function getApprovalsCountAttribute()
    {
        return (User::all()->approvals()->get()->count());
    }
}

