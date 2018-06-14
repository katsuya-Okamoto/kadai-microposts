<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\user;
use App\Micropost;

class UsersController extends Controller
{
    public function index()
    {
        $user=\Auth::user();
        $users = User::paginate(10);
        
        return view('users.index', [
            'user'=>$user,
            'users'=>$users,
        ]);
    }
    
    public function show($id)
    {
        $user = User::find($id);
        
        //ここ直さないとちゃんと出ない
        $microposts = $user->feed_microposts()->orderBy('created_at', 'desc')->paginate(10);
        
        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];
        
        $data += $this->counts($user);
        
        return view('users.show',$data);
    }
    
    public function followings($id)
    {
        $user = User::find($id);
        $followings = $user->followings()->paginate(10);
        
        $data = [
            'user' => $user,
            'users'=> $followings,
        ];
        
        $data += $this->counts($user);
        
        return view('users.followings', $data);
    }
    
    public function followers($id)
    {
        $user = User::find($id);
        $followers = $user->followers()->paginate(10);
        
        $data = [
            'user' =>$user,
            'users'=>$followers,
        ];
        
        $data += $this->counts($user);
        
        return view('users.followers', $data);
    }
    
    public function favorites($id)
    {
        $user = User::find($id);
        $favorites = $user->favorites()->paginate(10);
        
        $data = [
            'user' =>$user,
            'favorites'=>$favorites,
        ];
        
        $data += $this->counts($user);
        
        return view('users.favorites', $data);
    }

}