<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DetailCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DetailCartController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // --- CARI ACTIVE CART
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

            $activeCart = Cart::query()
                // ->with(['detailCart.produk', 'detailCart.hampers'])
                // ->withCount('detailCart')
                ->where('customer_id', $user->id)
                ->where('status_cart', 1)
                ->first(); // karena 1 aja cartnya  

            if (!$activeCart) {
                // TODO::: bikin cart baru ???
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Cart tidak ditemukan.',
                    ],
                    404
                );
            }

            $detailCartRequest = $request->all();

            // TODO::: VALIDATOR LEBIH RUMIT & AMAN
            $validate = Validator::make($detailCartRequest, [
                'produk_id' => 'exists:produks,id',
                'hampers_id' => 'exists:hampers,id',
                'jumlah' => 'required',
                'harga_produk_sekarang' => 'required',
            ]);

            if ($validate->fails()) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => $validate->messages(),
                    ],
                    400
                );
            }

            if (!$detailCartRequest['produk_id'] && !$detailCartRequest['hampers_id']) {
                return response()->json(
                    [
                        'data' => null,
                        'message' => 'Produk/hampers harus ada di detail hampers.',
                    ],
                    400
                );
            }

            $detailCartRequest['cart_id'] = $activeCart->id;

            // create detail hampers
            // cek unik, dalam 1 hampers tidak boleh ada 2 produk yang sama namun beda jumlah produk
            // jika ada produk yang sama maka jumlahnya diambil dari hasil penjumlahan keduanya

            if ($detailCartRequest['produk_id']) {
                $detailCart = DetailCart::query()
                    ->where('cart_id', $activeCart->id)
                    ->where('produk_id', $detailCartRequest['produk_id'])
                    ->first();

                if ($detailCart) {
                    $detailCart->jumlah = $detailCart->jumlah + $detailCartRequest['jumlah'];
                    $detailCart->save();
                } else {
                    $detailCart = DetailCart::create($detailCartRequest);
                }
            } else {
                $detailCart = DetailCart::query()
                    ->where('cart_id', $activeCart->id)
                    ->where('hampers_id', $detailCartRequest['hampers_id'])
                    ->first();

                if ($detailCart) {
                    $detailCart->jumlah = $detailCart->jumlah + $detailCartRequest['jumlah'];
                    $detailCart->save();
                } else {
                    $detailCart = DetailCart::create($detailCartRequest);
                }
            }


            return response()->json(
                [
                    'data' => $detailCart,
                    'message' => 'Berhasil membuat data detail hampers baru dari hampers ' . $hampersData->nama_hampers . '.'
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

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     try {
    //         $detailHampersData = DetailHampers::with('produk')->find($id);

    //         if (!$detailHampersData) {
    //             return response()->json(
    //                 [
    //                     'data' => null,
    //                     'message' => 'Detail hampers tidak ditemukan.',
    //                 ],
    //                 404
    //             );
    //         }

    //         return response()->json(
    //             [
    //                 'data' => $detailHampersData,
    //                 'message' => 'Berhasil mengambil 1 data detail hampers.',
    //             ],
    //             200
    //         );
    //     } catch (Throwable $th) {
    //         return response()->json(
    //             [
    //                 'data' => null,
    //                 'message' => $th->getMessage(),
    //             ],
    //             500
    //         );
    //     }
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     try {
    //         $detailHampersDataUpdated = DetailHampers::find($id);

    //         if (!$detailHampersDataUpdated) {
    //             return response()->json(
    //                 [
    //                     'data' => null,
    //                     'message' => 'Detail hampers tidak ditemukan.',
    //                 ],
    //                 404
    //             );
    //         }

    //         $detailHampersDataRequest = $request->all();

    //         $validate = Validator::make($detailHampersDataRequest, [
    //             'hampers_id' => 'required|exists:hampers,id',
    //             'produk_id' => 'required|exists:produks,id',
    //             'jumlah_produk' => 'required',
    //         ]);

    //         if ($validate->fails()) {
    //             return response()->json(
    //                 [
    //                     'data' => null,
    //                     'message' => $validate->messages(),
    //                 ],
    //                 400
    //             );
    //         }

    //         // update detail hampers
    //         $detailHampersDataUpdated->update($detailHampersDataRequest);

    //         return response()->json(
    //             [
    //                 'data' => $detailHampersDataUpdated,
    //                 'message' => 'Berhasil mengupdate data detail hampers.'
    //             ],
    //             200
    //         );
    //     } catch (Throwable $th) {
    //         return response()->json(
    //             [
    //                 'data' => null,
    //                 'message' => $th->getMessage(),
    //             ],
    //             500
    //         );
    //     }
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     try {
    //         $detailHampersDataDeleted = DetailHampers::find($id);

    //         if (!$detailHampersDataDeleted) {
    //             return response()->json(
    //                 [
    //                     'data' => null,
    //                     'message' => 'Detail hampers tidak ditemukan.',
    //                 ],
    //                 404
    //             );
    //         }

    //         if (!$detailHampersDataDeleted->delete()) {
    //             return response()->json(
    //                 [
    //                     'data' => $detailHampersDataDeleted,
    //                     'message' => 'Gagal menghapus data detail hampers.',
    //                 ],
    //                 500
    //             );
    //         }

    //         return response()->json(
    //             [
    //                 'data' => $detailHampersDataDeleted,
    //                 'message' => 'Berhasil menghapus data detail hampers.',
    //             ],
    //             200
    //         );
    //     } catch (Throwable $th) {
    //         return response()->json(
    //             [
    //                 'data' => null,
    //                 'message' => $th->getMessage(),
    //             ],
    //             500
    //         );
    //     }
    // }
}
