<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public $messages;
    public function __construct()
    {
        parent::setMessage('product', array('store' => 'Producto creado exitosamente', 'updated' => 'Producto actualizado exitosamente', 'delete' => 'product deleted successfull'));

        $this->messages = parent::getMessage('product');

        $this->middleware('auth:api', ['except' => ['index', 'show', 'store']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json($products);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $image = $request->file('image')->store('public/images');
        $pathImage = Storage::url($image);

        $data = request()->all();
        $data['image'] = $pathImage;

        Product::create($data);
        $response = $this->messages['store'];
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->updated($request->all());
        $response = $this->messages['update'];
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();
        $response = $this->messages('delete');
        return response()->json($response);
    }
}
