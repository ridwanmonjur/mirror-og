<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\OrderProduct;
use App\Mail\OrderPlaced;
use App\NewCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CheckoutRequest;
use Stripe\Stripe;
use Stripe\Exception\CardException;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        
        if ($cart->getCount() == 0) {
            return redirect()->route('shop.index');
        }

        if (auth()->user() && request()->is('guestCheckout')) {
            return redirect()->route('checkout.index');
        }

        return view('shop.checkout')->with([
            'cart' => $cart,
            'discount' => getNumbers()->get('discount'),
            'newSubtotal' => getNumbers()->get('newSubtotal'),
            'newTotal' => getNumbers()->get('newTotal'),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutRequest $request)
    {
        // Check race condition when there are less items available to purchase
        if ($this->productsAreNoLongerAvailable()) {
            return back()->withErrors('Sorry! One of the items in your cart is no longer avialble.');
        }

        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        $contents = $cart->getContent()->map(function ($item) {
            return $item->product->slug.', '.$item->quantity;
        })->values()->toJson();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $charge = \Stripe\Charge::create([
                'amount' => getNumbers()->get('newTotal'),
                'currency' => 'MYR',
                'source' => $request->stripeToken,
                'description' => 'Order',
                'receipt_email' => $request->email,
                'metadata' => [
                    'contents' => $contents,
                    'quantity' => $cart->getCount(),
                    'discount' => collect(session()->get('coupon'))->toJson(),
                ],
            ]);

            $order = $this->addToOrdersTables($request, null);
            Mail::send(new OrderPlaced($order));

            $cart->clearItems();

            // decrease the quantities of all the products in the cart
            $this->decreaseQuantities();

            
            session()->forget('coupon');

            return redirect()->route('confirmation.index')->with('success_message', 'Thank you! Your payment has been successfully accepted!');
        } catch (CardException $e) {
            $this->addToOrdersTables($request, $e->getMessage());
            return back()->withErrors('Error! ' . $e->getMessage());
        }
    }


    protected function addToOrdersTables($request, $error)
    {
        // Insert into orders table
        $order = Order::create([
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'billing_email' => $request->email,
            'billing_name' => $request->name,
            'billing_address' => $request->address,
            'billing_city' => $request->city,
            'billing_province' => $request->province,
            'billing_postalcode' => $request->postalcode,
            'billing_phone' => $request->phone,
            'billing_name_on_card' => $request->name_on_card,
            'billing_discount' => getNumbers()->get('discount'),
            'billing_discount_code' => getNumbers()->get('code'),
            'billing_subtotal' => getNumbers()->get('newSubtotal'),
            'billing_tax' => 0,
            'billing_total' => getNumbers()->get('newTotal'),
            'error' => $error,
        ]);

        // Insert into order_product table
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        foreach ($cart->getContent() as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
        }

        return $order;
    }


    protected function decreaseQuantities()
    {
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        foreach ($cart->getContent() as $item) {
            $product = Product::find($item->product_id);

            $product->update(['quantity' => $product->quantity - $item->quantity]);
        }
    }

    protected function productsAreNoLongerAvailable()
    {
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        foreach ($cart->getContent() as $item) {
            $product = Product::find($item->product_id);
            if ($product->quantity < $item->quantity) {
                return true;
            }
        }

        return false;
    }
}
