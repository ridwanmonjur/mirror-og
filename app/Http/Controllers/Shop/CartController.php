<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ShopService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $userId = auth()->id();
        $data = $this->shopService->getCartData($userId);
        
        return view('shop.cart')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product): RedirectResponse
    {
        $userId = auth()->id();
        $result = $this->shopService->addItemToCart($userId, $product);
        
        if ($result['success']) {
            return redirect()->route('cart.index')->with('success_message', $result['message']);
        } else {
            return redirect()->route('cart.index')->with('errors', collect([$result['message']]));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $userId = auth()->id();
        
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|between:1,20'
        ]);

        if ($validator->fails()) {
            session()->flash('errors', collect(['Quantity must be between 1 and 20.']));
            return response()->json(['success' => false], 400);
        }

        $result = $this->shopService->updateCartItem(
            $userId,
            $id,
            $request->quantity,
            $request->productQuantity
        );
        
        if ($result['success']) {
            session()->flash('success_message', $result['message']);
            return response()->json(['success' => true]);
        } else {
            session()->flash('errors', collect([$result['message']]));
            return response()->json(['success' => false], 400);
        }
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
        $result = $this->shopService->removeItemFromCart($userId, $id);
        
        return back()->with('success_message', $result['message']);
    }

   
}
