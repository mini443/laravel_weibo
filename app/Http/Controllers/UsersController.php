<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Mail;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store','confirmEmail','index']
        ]);
    }
    //创建用户
    public function create(){
        return view('users.create');
    }

    //所有用户
    public function index(){
        $users=User::paginate(10);
        return view('users.index',compact('users'));
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

    /**
     * 数据验证
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
//        Auth::login($user);
//        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
//        return redirect()->route('users.show',[$user]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    /**
     * 发送邮件
     * @param $user
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'summer@example.com';
        $name = 'apex';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    /**
     * 编辑用户信息
     * @param User $user
     */
    public function edit(User $user){
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    /**
     * 更新用户信息
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(User $user, Request $request)
    {
        $this->authorize('update',$user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        return redirect()->route('users.show', $user->id);
    }

    /**
     * 邮箱认证
     * @param $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
