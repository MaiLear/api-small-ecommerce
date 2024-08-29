<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public $messages;
    public function __construct()
    {
        parent::setMessage('customer', array(
        'store' => 'Cliente creado exitosamente',
        'update' => 'Cliente actualizado exitosamente', 
        'delete' => 'Cliente eliminado exitosamente'));
        $this->messages = parent::getMessage('customer');

        $this->middleware('auth:api', ['except' => ['index','show','store']]);

    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customers = Customer::all();
        return response()->json($customers);
    }


 



    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        $request['password'] = Hash::make($request['password']);
        Customer::create($request->all());
        $response = $this->messages['store'];
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

 

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, string $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->all());
        $response = $this->messages['update'];
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        $response = $this->messages['delete'];
        return response()->json($response);
    }
}
