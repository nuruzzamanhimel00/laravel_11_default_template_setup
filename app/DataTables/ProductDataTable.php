<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ProductDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('id', function ($row) {
                return '<input type="checkbox" class="product_checkbox" name="product_id[]" value="' . $row->id . '">';
            })
            ->addColumn('price', function (Product $product) {
                // $defaultPrice = $product->defaultPrice;
                $defaultStock = $product->defaultWarehouseStock;
                // return $defaultPrice ? number_format($defaultPrice->regular_price, 2) : 'N/A';
                return $defaultStock ? number_format($defaultStock->slate_price, 2) : 'N/A';
            })
            ->addColumn('stock_quantity', function (Product $product) {
                $defaultStock = $product->defaultWarehouseStock;
                return $defaultStock ? formatNumberSmart($defaultStock->stock_quantity)  : 'N/A';
            })
            ->editColumn('image', function (Product $product) {
                $image = getStorageImage($product->image);
                return '<img src="' . $image . '" width="40" height="40">';
            })
            ->editColumn('is_variant', function (Product $product) {
                $text = $product->is_variant ? "Yes" : "No";
                $class = $product?->is_variant ? "badge-variant" : "badge-non-variant";
                return '<span class="' . $class . '">' . ucfirst($text) . '</span>';
            })
            ->editColumn('category_id', function (Product $product) {
                return $product->category?->name ?? '';
            })
            ->editColumn('status', function ($item) {
                $badge = $item->status == STATUS_ACTIVE ? "bg-success" : "bg-danger";
                return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
            })
            ->editColumn('price', function ($item) {
                $html = '';
                $html .= 'Purchase Price: '.addCurrency(formatNumberSmart($item->purchase_price)) .' <br/>';
                $html .= 'User Price: '.addCurrency(formatNumberSmart($item->sale_price)) .' <br/>';
                $html .= 'Restaurant Price: '.addCurrency(formatNumberSmart($item->restaurant_sale_price)) .' <br/>';
                return $html;
            })
            ->editColumn('name', function ($item) {
                $review_rating = $item->review_ratings();
                $averageRating = $review_rating['averageRating'];
                $totalReviewCount = $review_rating['totalReviewCount'];
                $name = $item->name.' <i class="fas fa-star rating" ></i>  '.formatNumberSmart($averageRating).' ('.$totalReviewCount.')';
                return $name;
            })
            ->addColumn('action', function ($item) {
                $buttons = '';

                if (auth()->user()->can('Edit Product')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('products.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
                }
                if (auth()->user()->can('Download Product Barcode')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('product.barcode.download', $item->id) . '"><i class="mdi mdi-download"></i> Download Barcode</a></li>';
                }
                if (auth()->user()->can('Reviews Product')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('product.reviews', $item->id) . '"><i class="fas fa-star"></i> Reviews</a></li>';
                }
                if (auth()->user()->can('Delete Product')) {
                    $buttons .= '<form action="' . route('products.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $item->id . ')" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
                    ';
                }
                return '<div class="dropdown ic_custom_table">
                              <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                              '. $buttons .'
                              </ul>
                        </div>';


            })
            ->rawColumns(['image', 'status', 'is_variant', 'action','id','name','price'])
            ->addIndexColumn();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->latest()
            ->with([
                'category:id,name',
                'defaultWarehouseStock.warehouse',
                'reviews:id,product_id,rating',
            ])->newQuery();
    }



    public function html()
    {
        $params             = $this->getBuilderParameters();
        $params['order']    = [[2, 'asc']];

        $customBtn = [
            Button::make()
            ->text('Download All Barcode')
            ->addClass('btn btn-outline-primary text-dark custom-barcode-btn buttons-download')

            ->attr([
                'id' => 'download_barcode',
                'title' => 'Download All Barcode'
            ]),

        ];
        $buttons = $this->getDynamicDataTableButtons(null,$customBtn);

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction([
                    'width' => '55px',
                    'class' => 'text-center',
                    'printable' => false,
                    'exportable' => false,
                    'title' => 'Action'
                ])
            ->parameters($params)
            ->buttons(
                ...$buttons
            );
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('SL')),
            Column::make('id', 'id')
            ->orderable(false)
            ->searchable(false)
            ->printable(false)
            ->exportable(false)
            ->className('select_all_checkbox')
            ->title('<input type="checkbox" class="select_all"/>')
            ->render(''),
            Column::make('image', 'image')->title(__('Thumb'))->sortable(false),
            Column::make('name', 'name')->title(__('Product Name')),
            // Column::make('sku', 'sku')->title(__('SKU')),
            Column::make('status', 'status')->title(__('Status'))->sortable(false),
            Column::make('category_id', 'category.name')->title(__('Category')),
            Column::make('price')->title('Price'),
            Column::make('stock_quantity')->title(__('Stock Quantity')),
            // Column::make('is_variant', 'is_variant')->title(__('Variant')),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Product_' . date('YmdHis');
    }
    protected function createNewItem($createRoute): string
    {
        return "window.location = '" . $createRoute . "';";
    }
}
