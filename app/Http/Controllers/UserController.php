<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Session;

class UserController extends Controller
{
    public function getSignup() {
        return view('user.signup');
    }

    public function postSignup(Request $request){
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'email|required|unique:users',
            'password' => 'required|min:4'
        ]);
        
        //Another way of forming an object
         
        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'  => bcrypt($request->input('password'))
        ]);
        /* This is same as
          $user = new User();
          $user->email = $request->input('email');
          $user->password = bcrypt($request->input('password'));
        */ 
        $user->save();

        Auth::login($user);

        if(Session::has('oldUrl')){
            $oldUrl = Session::get('oldUrl');
            Session::forget('oldUrl');
            return redirect()->to($oldUrl);
        }

        return redirect()->route('user.profile');
    }


    public function getSignin() {
        return view('user.signin');
    }

    public function postSignin(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required|min:4'
        ]);
    
      
       if(Auth::attempt(['name' => $request->input('name'),  //for this to work 'name' should be 'unique'
                         'password' => $request->input('password')])){

            if(Session::has('oldUrl')){
                $oldUrl = Session::get('oldUrl');
                Session::forget('oldUrl');
                return redirect()->to($oldUrl);
            }
            return redirect()->route('user.profile');
        }

        //return redirect()->back();
        return redirect('/signin')->with('error', 'Invalid Name or Password');
        
    }

    public function getProfile() {
        $orders = Auth::user()->orders;
        
        if((is_countable($orders) ? count($orders) : 0) > 0) {
            $orders->transform(function($order, $key){
                $order->cart = unserialize($order->cart);
                return $order;
            });
        }

        return view('user.profile', ['orders' => $orders]);
    }

    public function getLogout() {
        Auth::logout();
       // return redirect()->back();

        return redirect()->route('user.signin');
    }


}
