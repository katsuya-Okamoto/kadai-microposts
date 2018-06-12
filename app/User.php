<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    //protected $table = 'freeTableName';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts(){
        return $this->hasMany(Micropost::class);
    }
    
    //User「が」フォローしている他のユーザー
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    //User「を」フォローしている他のユーザー
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        //既にフォローしているのかの確認
        $exist = $this->is_following($userId);
        //自分自身ではないか確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me){
            //既にフォロー済みならなにもしない（自分でもない）
            return false;
        }else{
            //未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        //既にフォローしているのかの確認
        $exist = $this->is_following($userId);
        //自分自身ではないか確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me){
            //既にフォロー済みならフォローを外す
            $this->followings()->detach($userId);
            return true;
        }else{
            //未フォローであればなにもしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();   //usersテーブルのデータ
        $follow_user_ids[] = $this->id;
        //dd($follow_user_ids);
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
}
