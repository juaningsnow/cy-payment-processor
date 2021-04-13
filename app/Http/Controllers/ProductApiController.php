<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\ProductModel;
use App\Utils\FilterHelper;
use DateTime;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    use FilterHelper;
    private $query;
    protected $limit = 15;

    public function __construct(ProductModel $model)
    {
        $this->middleware('auth:api');
        $this->query = $model->newQuery();
    }

    public function index(Request $request)
    {
        $this->filter($request);
        return ProductResource::collection($this->query->paginate($this->getLimit($request)));
    }

    public function store(Request $request)
    {
        $product = ProductModel::create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
            'date_and_time' => new DateTime($request->input('date_and_time'))
        ]);
        foreach ($request->file('files') as $file) {
            $product->addMedia($file)->toMediaCollection();
        };
        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = ProductModel::find($id);
        return new ProductResource($product);
    }

    public function destroy($id)
    {
        $product = ProductModel::find($id);
        $product->delete();
        return response('success', 200);
    }
}
