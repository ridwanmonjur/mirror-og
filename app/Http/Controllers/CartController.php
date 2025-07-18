<?php

namespace App\Http\Controllers;

use App\Product;
use App\NewCart;
use App\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class CartController extends Controller
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
        
        $mightAlsoLike = Product::mightAlsoLike()->get();
        $top_pick = DB::table('products')->orderBy('id','DESC')->paginate(4);
        $top_pick2 = DB::table('products')->orderBy('id','ASC')->paginate(4);
        $discount = 0;
        $cartTotal = $cart->getTotal();
        
        return view('cart')->with([
            'cart' => $cart,
            'mightAlsoLike' => $mightAlsoLike,
            'top_pick' => $top_pick,
            'top_pick2' => $top_pick2,
            'discount' => $discount,
            'newSubtotal' => $cartTotal,
            'newTotal' => $cartTotal - $discount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product)
    {
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        
        try {
            $cart->addItem($product->id, 1, $product->price);
            return redirect()->route('cart.index')->with('success_message', 'Item was added to your cart!');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('errors', collect(['Maximum quantity of 20 exceeded for this item.']));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|between:1,20'
        ]);

        if ($validator->fails()) {
            session()->flash('errors', collect(['Quantity must be between 1 and 20.']));
            return response()->json(['success' => false], 400);
        }

        if ($request->quantity > $request->productQuantity) {
            session()->flash('errors', collect(['We currently do not have enough items in stock.']));
            return response()->json(['success' => false], 400);
        }

        $product = Product::find($id);
        if ($product) {
            $cart->updateItem($id, $request->quantity, $product->price);
        }
        
        session()->flash('success_message', 'Quantity was updated successfully!');
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userId = auth()->id();
        $cart = NewCart::getUserCart($userId);
        
        $cart->removeItem($id);

        return back()->with('success_message', 'Item has been removed!');
    }

   
}
