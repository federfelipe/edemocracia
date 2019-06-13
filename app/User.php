<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'uf', 'role_id', 'cpf', 'uuid',
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

    /* EM TESTE DE USO - NÃO MEXER     não está usando esse código, porém no 'User/Show' há uma menção $user->proposals_count
       User has many Proposals*/
    /*public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }*/

    /* EM TESTE DE USO - NÃO MEXER    ESTÁ SENDO USADA! - 'APOIAR UMA IDEIA'/'APOIAR PROPOSTA *************
    User approvals Proposals */
    public function approvals()
    {
        return $this->belongsToMany(Proposal::class, 'approvals', 'user_id', 'proposal_id');
    }

    /*EM TESTE DE USO - NÃO MEXER    não está usando esse código, porém no 'User/Show' há uma menção $user->likes_count
     User likes Proposals*/
    /*public function likes()
    {
        return $this->belongsToMany(Proposal::class, 'likes', 'user_id', 'proposal_id');
    }*/

    /* EM TESTE DE USO - NÃO MEXER     Não está sendo usado!
     User has only one State*/
    /* public function states()
     {
         return $this->hasOne(State::class);
     }*/

    /*EM TESTE DE USO - NÃO MEXER    Não está sendo usado!
    User has one Role*/
    /*public function roles()
    {
        return $this->hasOne(Role::class, 'id');
    }*/

    // Get is_admin attribute
    public function getIsAdminAttribute()
    {
        return Auth::user()->role_id === 0 or Auth::user()->role_id === 1 or Auth::user()->role_id === 2;
    }

    // Get is_super_user attribute
    public function getIsSuperUserAttribute()
    {
        return Auth::user()->role_id === 0;
    }

    // Get is_committee_user attribute
    public function getIsCommitteeUserAttribute()
    {
        return Auth::user()->role_id === 0 or Auth::user()->role_id === 2;
    }

    // Get Role Name
    public function getRoleNameAttribute()
    {
        return Role::find($this->role_id)->role;
    }

    // Get Proposals Count
    public function getProposalsCountAttribute()
    {
        return Proposal::where('user_id', $this->id)->count();
    }

    // Get Likes Count
    public function getLikesCountAttribute()
    {
        return Like::where('user_id', $this->id)->count();
    }

    // Get Approvals Count
    public function getApprovalsCountAttribute()
    {
        return Approval::where('user_id', $this->id)->count();
    }

    // Get Responded Count
    public function getProposalsRespondedCountAttribute()
    {
        return Proposal::where('responder_id', $this->id)->count();
    }

    public function getAvatarAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'?d=identicon'.'&s=80';
    }

    public function getCreatedAtAttribute($value)
    {
        return date('M y', strtotime($value));
    }

    public function getUserByEmail($email)
    {
        return self::Where('email', $email)->first();
    }

    // Socialite
    public function socialNetworks()
    {
        return $this->belongsToMany('App\SocialNetwork', 'social_users', 'user_id');
    }

    public function socialUser()
    {
        return $this->hasMany('App\SocialUser');
    }

    // Return users of system
    public function getUsersCommon()
    {
        return $this->where('role_id', '99')->get();
    }

    // Return users Adm, Approvers and Commission
    public function getUsersAdm()
    {
        return $this->whereIn('role_id', [0, 1, 2])->get();
    }
}
