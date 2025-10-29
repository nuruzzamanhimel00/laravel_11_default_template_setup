<?php

namespace App\Http\Controllers\Api\V1\Wishlist;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\PaginatedResourceTrait;

class WishlistController extends Controller
{
    use ApiResponse, PaginatedResourceTrait;

    public function __construct()
    {

    }

    public function wishList(Request $request)
    {
        $wishlist_uuid = $this->generateUUid($request->wishlist_uuid);
        $auth = auth('api')->user();

        $wishListItems = Wishlist::with('product')
            ->where('wishlist_uuid', $wishlist_uuid)
            ->get();

        if ($wishListItems->isEmpty()) {
            return response()->json([
                'message' => 'Wishlist data not available.',
                'success' => false,
                'items' => [],
            ], 200);
        }

        $items = $wishListItems->map(function ($item) use ($auth) {
            $product = $item->product;

            return [
                'id' => $item->id,
                'product_id' => $product->id ,
                'product_name' => $product->name ?? 'N/A',
                'product_image_url' => $product->image_url ?? null,
                'product_price' => $auth
                    ? ($auth->type == User::TYPE_REGULAR_USER
                        ? addCurrency($product->sale_price ?? 0)
                        : addCurrency($product->restaurant_sale_price ?? 0))
                    : addCurrency($product->sale_price ?? 0),
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }


    public function removeToWishlist(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
        ]);

        try {
            DB::beginTransaction();


            $wishList = Wishlist::find($request->id);
            if (!$wishList) {
                return response()->json(['message' => 'Wish data not available.'], 200);
            }

            $wishList->delete();


            DB::commit();

            return response()->json([
                'message' => 'Item remove form wishlist successfully.',

                'success' => true,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function getWishlistUUid(){
        $auth = auth('api')->user();
        if (!$auth) return;
        $wishList = Wishlist::where('user_id', $auth->id)->first();
        if($wishList) return $wishList->wishlist_uuid;
        return ;
    }

    public function generateUUid($wishlist_uuid = null){
        $getWishlistUuid = $this->getWishlistUUid();
        $wishlist_uuid = $wishlist_uuid ? $wishlist_uuid : (
            $getWishlistUuid ? $getWishlistUuid : generateUuid()
        );
        return $wishlist_uuid;
    }
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'wishlist_uuid' => 'nullable|string',
        ]);

        $wishlist_uuid = $this->generateUUid($request->wishlist_uuid);
        $auth = auth('api')->user();

        try {
            DB::beginTransaction();

            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['message' => 'Product not available.'], 200);
            }

            $wishList = Wishlist::firstOrNew([
                'wishlist_uuid' => $wishlist_uuid,
                'product_id' => $product->id,
            ]);

            // If wishlist already exists, user_id will be updated
            $wishList->user_id = $auth->id ?? null;
            $wishList->save();

            // Optional: update other null-user wishlist items under the same UUID
            $this->wishListUserIdUpdate($wishlist_uuid);

            DB::commit();

            return response()->json([
                'message' => 'Wishlist added successfully.',
                'wishList' => $wishList,
                'wishlist_uuid' => $wishlist_uuid,
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function wishListUserIdUpdate($wishlist_uuid)
    {
        $auth = auth('api')->user();
        if (!$auth) return;

        Wishlist::where('wishlist_uuid', $wishlist_uuid)
            ->whereNull('user_id')
            ->update(['user_id' => $auth->id]);
    }


}
