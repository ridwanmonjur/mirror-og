<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends Controller
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
    public function index(Request $request)
    {
        $data = $this->shopService->getProductsWithFilters($request);
        
        return view('shop.shop')->with($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $data = $this->shopService->getProductDetails($slug);
        
        return view('shop.product')->with($data);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3',
        ]);

        $query = $request->input('query');
        $products = $this->shopService->searchProducts($query);

        return view('shop.search-results')->with('products', $products);
    }

    public function searchAlgolia(Request $request)
    {
        return view('shop.search-results-algolia');
    }
}
