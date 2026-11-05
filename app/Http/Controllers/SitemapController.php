<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $products   = Product::where('active', true)->get(['slug', 'updated_at']);
        $categories = Category::all(['slug', 'updated_at']);

        $content = view('seo.sitemap', compact('products', 'categories'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /a33mgr\nDisallow: /mayoristas/panel\nSitemap: " . route('sitemap');
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
