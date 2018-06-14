<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFavoriteController extends Controller
{
    public function store(Request $request, $postId)
    {
        \Auth::user()->favorite($postId);
        return redirect()->back();
    }
    
    public function destroy($postId)
    {
        \Auth::user()->unfavorite($postId);
        return redirect()->back();
    }
}
