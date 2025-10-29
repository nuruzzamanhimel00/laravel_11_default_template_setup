<?php

namespace App\DataTables;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Promotion;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class LowStockProductDataTable extends DataTable
{

    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)


            ->editColumn('image', function (Product $product) {
                $image = getStorageImage($product->image);
                return '<img src="' . $image . '" width="40" height="40">';
            })

            ->addColumn('stock_alert_quantity', function (Product $product) {
                return LOW_STOCK_ALERT;
            })
            ->rawColumns(['image'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model)
    {
        $product_id = request()->product_id ?? null;
        return $model
            ->newQuery()
            ->where('total_stock_quantity', '<=', LOW_STOCK_ALERT)
            ->when($product_id, function ($q) use ($product_id) {
                return $q->where('id', $product_id);
            })
            ->with(['category'])
            ->orderBy('products.created_at', 'desc'); // Fix ambiguity
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $params             = $this->getBuilderParameters();
        $params['order']    = [[2, 'asc']];

        $buttons = $this->getDynamicDataTableButtons(null,);

           // Add custom reset button
            $buttons[] = [
            'text' => '<i class="fas fa-sync-alt"></i> Reset',
            'className' => 'dt-button buttons-excel',
            'action' => 'function (e, dt, node, config) {
                window.location.href = "' . route('product.low.stock') . '";
            }',
        ];


        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->addAction(['width' => '55px', 'class' => 'text-center', 'printable' => false, 'exportable' => false, 'title' => 'Action'])
            ->parameters($params)
            ->buttons(...$buttons);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            Column::computed('DT_RowIndex', __('SL')),
            Column::make('image', 'image')->title(__('Thumb'))->sortable(false),
            Column::make('name', 'name')->title(__('Product Name')),
            Column::make('barcode', 'barcode')->title(__('Barcode')),
            Column::make('category.name', 'category.name')->title(__('Category')),
            Column::make('total_stock_quantity', 'total_stock_quantity')->title(__('Total Stock Quantity')),
            Column::make('stock_alert_quantity', 'stock_alert_quantity')->title(__('Stock Alert Quantity')),

        ];


        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'LowStockProducts_' . date('YmdHis');
    }

    /**
     * pdf
     *
     * @return void
     */
    public function pdf()
    {
        $data = $this->getDataForExport();

        $pdf = PDF::loadView('vendor.datatables.print', [
            'data' => $data
        ]);
        return $pdf->download($this->getFilename() . '.pdf');
    }
}
