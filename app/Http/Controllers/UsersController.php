<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UsersController extends Controller
{
    //创建用户
    public function create(){
        return view('users.create');
    }

    //所有用户
    public function index(){
        return '主页';
    }

    /**
     * 单个用户
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        $user=compact('user');
//        dump($user->name);
//        exit();
        return view('users.show', $user);
    }

}
