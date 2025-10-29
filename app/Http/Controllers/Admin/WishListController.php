<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Services\PromotionService;
use App\Http\Controllers\Controller;
use App\DataTables\WishListDataTable;
use Illuminate\Http\RedirectResponse;

use App\DataTables\PromotionDataTable;
use App\Http\Requests\PromotionRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;



class WishListController extends Controller
{
    // protected $promotionService;

    // public function __construct(PromotionService $promotionService)
    // {
    //     $this->promotionService = $promotionService;

    // }

    /**
     * Define middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('List Promotion'), only: ['index']),
        ];
    }


    public function index(WishListDataTable $dataTable)
    {
        setPageMeta('Wish List');

        setCreateRoute(null);

        return $dataTable->render('admin.promotion.index');
    }

    /**
     * create
     *
     * @return void
     */
    public function create(Request $request): \Illuminate\View\View
    {

    }




    public function store(PromotionRequest $request) : \Illuminate\Http\RedirectResponse
    {

    }

    public function edit(Request $request, $id) : \Illuminate\View\View
    {

    }
    public function show($id) : \Illuminate\View\View
    {
        setPageMeta('Show Promotion');
        setCreateRoute(null);
        $promotion = $this->promotionService->get($id)->load([
            'promotion_items.category',
            'promotion_items.product',
        ]);

        return view('admin.promotion.show', compact('promotion'));
    }


    public function update(PromotionRequest $request, $id): RedirectResponse
    {

    }

    public function destroy($id) : RedirectResponse
    {

    }
    public function restore($id) : RedirectResponse
    {

    }

}
