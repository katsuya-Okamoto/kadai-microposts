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
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'micropost_user','user_id','micropost_id')->withTImestamps();
    }
    
    public function is_favorite($post_id)    
    {
        return $this->favorites()->where('micropost_id', $post_id)->exists();
    }
    
    public function favorite($post_id)
    {
        //既にお気に入りにしているか確認
        $exists = $this->is_favorite($post_id);
        //自分自身のmicropostでもお気に入りにしていいのでここでは確認不用
        
        if ($exists){
            //すでにお気に入りにしていればなにもしない
            return false;
        }else{
            //お気に入りでなければお気に入りにする
            $this->favorites()->attach($post_id);
            return true;
        }
    }
    
    public function unfavorite($post_id)
    {
        //既にお気に入りにしているか確認
        $exists = $this->is_favorite($post_id);
        //自分自身のmicropostでもお気に入りにしていいのでここでは確認不用
        
        if ($exists){
            //すでにお気に入りにしていればお気に入りを外す
            $this->favorites()->detach($post_id);
            return true;
        }else{
            //お気に入りでなければお何もしない
            return false;
        }
    }
}
