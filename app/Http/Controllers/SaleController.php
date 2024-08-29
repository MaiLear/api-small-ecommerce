<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;


class SaleController extends Controller
{
    public $messages;


    public function __construct()
    {
        parent::setMessage('sale', array('store' => 'Venta creada exitosamente', 'update' => 'Venta actualizada exitosamente', 'delete' => 'Venta eliminada exitosamente', 'stock' => array('errors' => 'Cantidad de venta invalida')));
        $this->messages = parent::getMessage('sale');

        // $this->middleware('auth')->only(['store']);

        $this->middleware('auth:api', ['except' => ['index','show']]);


    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $sales = sale::all();
        return response()->json($sales);
    }


    private function invalidateStock(int $idProduct, int $quantitySale): bool
    {
        $product = Product::findOrFail($idProduct);
        $productStock = $product->stock;
        $result = $productStock - $quantitySale;
        return $result <= 0;
    }

    private function discountProductStock(int $idProduct,int $quantity){
        $product = Product::findOrFail($idProduct);
        $product->stock -= $quantity;
        $product->save(); 
    }

    private function discount(int $subotal): int
    {

        $discount = (5 * $subotal) / 100;
        $newSubtotal = $subotal - $discount;
        return $newSubtotal;
    }


    private function calculateSubtotal(int $idProduct, int $quantitySale): int
    {
        $product = Product::findOrFail($idProduct);
        $productPrice = $product->price;
        $subtotal = $productPrice * $quantitySale;
        return $quantitySale > 5 ? $this->discount($subtotal) : $subtotal;
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleRequest $request): JsonResponse
    {
        $idProduct = $request->product_id;
        $quantitySale = $request->quantity;
        if ($this->invalidateStock($idProduct, $quantitySale)) {
            $response = $this->messages['stock'];
            return response()->json($response);
        }

        $request['subtotal'] = $this->calculateSubtotal($idProduct, $quantitySale);

        Sale::create($request->all());
        $this->discountProductStock($idProduct,$quantitySale);
        $response = $this->messages['store'];
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $sale = Sale::findOrFail($id);
        return response()->json($sale);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(SaleRequest $request, string $id): JsonResponse
    {
        $sale = Sale::findOrFail($id);
        $sale->update($request->all());
        $response = $this->messages['update'];
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        $response = $this->messages['delete'];
        return response()->json($response);
    }
}
