<?php

namespace App\DataTables;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Promotion;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class WishListDataTable extends DataTable
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


            ->addColumn('product.image', function ($item) {
                return '<img class="ic-list-img" src="' . $item->product->image_url . '" alt="' . $item->product->name . '" />';
            })

            ->rawColumns(['product.image'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Wishlist $model)
    {
        return $model
            ->newQuery()
            ->whereNotNull('user_id')
            ->with(['product', 'customer'])
            ->orderBy('wishlists.created_at', 'desc'); // Fix ambiguity
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
            Column::make('customer.first_name', 'customer.first_name')->title(__('Customer/ Restaurant Name')),
            Column::make('product.image', 'product.image')->title(__('Product Image')),
            Column::make('product.name', 'product.name')->title(__('Product Name')),

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
        return 'WishList_' . date('YmdHis');
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
