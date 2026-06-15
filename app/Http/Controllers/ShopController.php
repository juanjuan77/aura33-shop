<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function home()
    {
        $featuredProducts = Cache::remember('home.featured', 300, fn() =>
            Product::where('active', true)->where('featured', true)
                ->with('category')->orderBy('sort_order')->limit(6)->get()
        );

        $categories = Cache::remember('categories.active', 300, fn() =>
            Category::where('active', true)
                ->withCount(['products' => fn($q) => $q->where('active', true)])
                ->orderBy('sort_order')->get()
        );

        return view('shop.home', compact('featuredProducts', 'categories'));
    }

    public function shop(Request $request)
    {
        $query = Product::where('active', true)->with('category');

        if ($request->categoria) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->categoria));
        }

        if ($request->buscar) {
            $query->where('name', 'like', '%' . $request->buscar . '%');
        }

        $products   = $query->orderBy('sort_order')->paginate(12);
        $categories = Cache::remember('categories.nav', 300, fn() =>
            Category::where('active', true)->orderBy('sort_order')->get()
        );

        if ($request->ajax()) {
            $html = $products->map(fn($p) => view('shop._product_card', ['product' => $p])->render())->implode('');
            return response()->json(['html' => $html, 'hasMore' => $products->hasMorePages()]);
        }

        return view('shop.shop', compact('products', 'categories'));
    }

    public function category(Request $request, Category $category)
    {
        $products = $category->products()
            ->where('active', true)
            ->orderBy('sort_order')
            ->paginate(12);

        $categories = Cache::remember('categories.nav', 300, fn() =>
            Category::where('active', true)->orderBy('sort_order')->get()
        );

        if ($request->ajax()) {
            $html = $products->map(fn($p) => view('shop._product_card', ['product' => $p])->render())->implode('');
            return response()->json(['html' => $html, 'hasMore' => $products->hasMorePages()]);
        }

        return view('shop.shop', compact('products', 'categories', 'category'));
    }

    public function product(Product $product)
    {
        abort_if(! $product->active, 404);

        $related = Product::where('active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }

    public function about()
    {
        return view('shop.about');
    }
}
