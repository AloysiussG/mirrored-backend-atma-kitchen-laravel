<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Throwable;

class CustomerController extends Controller
{
    public function index(Request $request){
        try{
            $customerQuery = Customer::query();
        if($request->has('search')){
            $customerQuery->where('nama', 'like', '%'.$request->search.'%');
        }
        if($request->has('email')){
            $customerQuery->where('email', 'like', '%'.$request->email.'%');
        }
        if($request->has('no_telp')){
            $customerQuery->where('no_telp', 'like', '%'.$request->no_telp.'%');
        }

        if ($request->sortBy && in_array($request->sortBy, ['id', 'nama', 'email', 'no_telp'])) {
            $sortBy = $request->sortBy;
        } else {
            $sortBy = 'id';
        }

        if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
            $sortOrder = $request->sortOrder;
        } else {
            $sortOrder = 'desc';
        }

        $customers = $customerQuery->orderBy($sortBy, $sortOrder)->get();
        return response([
            'message' => 'Retrieve All Success',
            'data' => $customers
        ],200);

        }
        catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }
        catch(Throwable $e){
            return response([
                'message' => $e->getMessage(),
                'data' => null
            ],404);
        }

    }
}
