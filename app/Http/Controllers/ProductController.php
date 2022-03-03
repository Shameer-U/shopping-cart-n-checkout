<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Cart;

use App\Models\Order;
use App\Http\Requests;
use App\Models\Product;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;


class ProductController extends Controller
{
    public function getIndex(){

        $products = Product::all(); 
        return view('shop.index', ['products' => $products]);
    }

    public function getAddToCart(Request $request, $id){
        $product = Product::find($id);
        //$oldCart = Session::has('cart') ? Session::get('cart') : null;
        $oldCart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
        
        //dd(print_r($oldCart));
        $cart = new Cart($oldCart);
        $cart->add($product, $product->id);

        $request->session()->put('cart' , $cart);
        
        return redirect()->route('product.index');
        
    }

    public function getCart() {

        if(!Session::has('cart')){
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        return view('shop.shopping-cart', ['products' => $cart->items,
        'totalPrice' => $cart->totalPrice]);

    }

    public function getCheckout() {
        if(!Session::has('cart')){
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $total = $cart->totalPrice;
        //return view('shop.checkout-cashier', ['total' => $total]);


        /** checkout with stripe only */
        \Stripe\Stripe::setApiKey( env('STRIPE_SECRET') );

        $payment_intent = \Stripe\PaymentIntent::create([
			'amount' => $total * 100,
			'currency' => 'inr',
			'description' => 'Test Payment with stripe',
			'payment_method_types' => ['card'],
		]);

		$intent = $payment_intent->client_secret;

        return view('shop.checkout-stripe', ['total' => $total, 'intent'=>$intent]);
    }

    // public function postCheckout(Request $request){
    //     if(!Session::has('cart')){
    //         return redirect()->route('shop.shopping-cart');
    //     }
    //     $oldCart = Session::get('cart');
    //     $cart = new Cart($oldCart);

    //      Stripe::setApiKey('sk_test_HZrQDqIEHaWTPe9Kl0L7NDX0');
    //      try{
    //         $charge = Charge::create([
    //             "amount" => $cart->totalPrice * 100,
    //             "currency" => "usd",
    //             "source" => "tok_visa",//$request->input('stripeToken'), // obtained with Stripe.js
    //             "description" => "Test Charge"
    //           ]);

    //           $order = new Order();
    //           $order->cart = serialize($cart);
    //           $order->address = $request->input('address');
    //           $order->name = $request->input('name');
    //           $order->payment_id = $charge->id;

    //           Auth::user()->orders()->save($order);
    //      } catch(\Exception $e){
    //          return redirect()->route('checkout')->with('error' , $e->getMessage());

    //      }

    //      Session::forget('cart');
    //      return redirect()->route('product.index')->with('success', 'Successfully purchased products');
    // }

    public function checkoutCashier(Request $request) {
        if(!Session::has('cart')){
            return redirect()->route('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);

        try{
            $user = Auth::user();

            // $stripeCharge  = $user->charge(
            //     $cart->totalPrice *100, $request->paymentMethodId, ['off_session' => true]
            // );

            $stripeCharge  = $user->charge(
                $cart->totalPrice *100, $request->paymentMethodId
            );


         } catch(IncompletePayment $e) {
             return redirect()->route(
                 'cashier.payment', [$e->payment->id, 'redirect' => route('checkout')]
                );
         }

    } 

    public function postCheckoutStripe(Request $request) {
        if(!Session::has('cart')){
            return redirect()->route('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);

        $order = new Order();
        $order->cart = serialize($cart);
        $order->address = $request->input('address');
        $order->name = $request->input('name');
        $order->payment_id = $charge->id;

        return redirect()->route('product.index');
    }

    public function afterPayment()
    {
        echo 'Payment Received, Thanks you for using our services.';
    }

}
