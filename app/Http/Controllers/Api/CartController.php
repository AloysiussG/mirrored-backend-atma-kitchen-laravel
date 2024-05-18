<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Throwable;

class CartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // bukan customer kalau punya role, throw 401
            if (isset($user['role_id'])) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'User bukan customer, tidak dapat melihat cart.'
                    ],
                    401
                );
            }

            $cartQuery = Cart::query()
                ->with(['detailCart.produk', 'detailCart.hampers'])
                ->withCount('detailCart')
                ->where('customer_id', $user->id)
                ->where('status_cart', 1); // cart yang sedang aktif

            // if ($request->search) {
            //     $cartQuery->where('nama_hampers', 'like', '%' . $request->search . '%');
            // }

            // if ($request->produk) {
            //     $cartQuery->whereHas('detailHampers.produk', function ($query) use ($request) {
            //         $query->where('nama_produk', 'like', '%' . $request->produk . '%');
            //     });
            // }

            if ($request->sortBy && in_array($request->sortBy, ['id', 'nama_hampers', 'created_at', 'harga_hampers'])) {
                $sortBy = $request->sortBy;
            } else {
                $sortBy = 'id';
            }

            if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
                $sortOrder = $request->sortOrder;
            } else {
                $sortOrder = 'desc';
            }

            // // SORT METHOD #2
            // $arraySort = [
            //     'terbaru',
            //     'terlama',
            //     'harga tertinggi',
            //     'harga terendah',
            //     'stok terbanyak',
            //     'kuota terbanyak',
            // ];

            // $arraySortValue = [
            //     ['name' => "Terbaru", 'sortBy' => "id", 'sortOrder' => "desc"],
            //     ['name' => "Terlama", 'sortBy' => "id", 'sortOrder' => "asc"],
            //     ['name' => "Harga tertinggi", 'sortBy' => "harga_hampers", 'sortOrder' => "desc"],
            //     ['name' => "Harga terendah", 'sortBy' => "harga_hampers", 'sortOrder' => "asc"],
            // ];

            // if ($request->sort && in_array(strtolower($request->sort), $arraySort)) {
            //     $key = array_search(strtolower($request->sort), $arraySort);
            //     $sortBy = $arraySortValue[$key]['sortBy'];
            //     $sortOrder = $arraySortValue[$key]['sortOrder'];
            // }

            $cart = $cartQuery->orderBy($sortBy, $sortOrder)->first(); // karena 1 aja cartnya

            // KALO ACTIVE CART TIDAK DITEMUKAN
            // TODO::: bikin cart baru ???

            return response()->json(
                [
                    'data' => $cart,
                    'message' => 'Berhasil mengambil data cart.'
                ],
                200
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'data' => null,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }
}
