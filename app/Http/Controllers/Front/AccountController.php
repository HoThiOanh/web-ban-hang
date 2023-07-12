<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Service\Order\OrderServiceInterface;
use App\Service\User\UserServiceInterface;
use App\Utilities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    private $userService;
    private $orderService;

    public function __construct(UserServiceInterface $userService, OrderServiceInterface $orderService)
    {
        $this->userService = $userService;
        $this->orderService = $orderService;
    }

    public function login()
    {
        return view('Front.Account.login');
    }
     public function checkLogin(Request $request)
     {
         $credentials = [
             'email' => $request->email,
             'password' => $request->password,
             'level' => Constant::user_level_client,
         ];

         $remember = $request->remember;

         if(Auth::attempt($credentials, $remember)) {
             //return redirect('');//trang chủ
             return redirect()->intended(''); //Mặc định là: trang chủ
         } else {
             return back()->with('notification', 'ERROR: Email or password is wrong');
         }
     }

     public function logout()
     {
         Auth::logout();

         return back();
     }

     public function register()
     {
         return view('Front.Account.register');
     }

     public function postRegister(Request $request)
     {
         if($request->password != $request->password_confirmation) {
             return back()->with('notification', 'ERROR: Confirm password does not match');
         }

         $data = [
             'name' => $request->name,
             'email' => $request->email,
             'password' => bcrypt($request->password),
             'level' => Constant::user_level_client,
         ];
         $this->userService->create($data);

         return redirect('account/login')->with('notification', 'Register Success! Please login.');
     }

     public function  myOrderIndex()
     {
         $orders = $this->orderService->getOrderByUserId(Auth::id());
         return view('Front.Account.my-order.index', compact('orders'));
     }

     public function myOrderShow($id)
     {
         $order = $this->orderService->find($id);
         return view('Front.Account.my-order.show', compact('order'));
     }
}
