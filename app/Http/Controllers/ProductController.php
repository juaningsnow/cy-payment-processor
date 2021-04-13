<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JavaScript;

class ProductController extends Controller
{
    public $availableFilters = [
        ['id' => 'keyword', 'text' => 'Keyword'],
        ['id' => 'category', 'text' => 'Category']
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('products.index', ['title' => 'Products']);
    }

    public function create()
    {
        return view('products.create', ['title' => "Product Create", 'id' => null]);
    }

    public function edit($id)
    {
        return view('products.edit', ['title' => "Product Edit", 'id' => $id]);
    }
}
