<?php

namespace App\DataTables;

use PDF;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use App\Models\PurchaseReturn;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class PurchaseReturnDataTable extends DataTable
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
            ->filterColumn('role', function ($query, $keyword) {
            })
            ->addColumn('action', function ($item) {
                $buttons = '';
                if (auth()->user()->can('Show Return Purchase')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('purchases.return.show', $item->id) . '" title="Edit"><i class="mdi mdi-eye mr-2"></i> ' . __('Show') . '</a></li>';
                }


                return '<div class="btn-group ic_custom_table">
                              <button class="btn btn-secondary dropdown-toggle"  type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu" role="menu" >
                              '. $buttons .'
                              </ul>
                        </div>';



            })

            ->editColumn('total', function ($item) {
                return addCurrency($item->total);
            })

            ->addColumn('total_product', function ($item) {
                return $item->purchase_return_items->count();
            })


            ->rawColumns([ 'status','created_at', 'action'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseReturn $model)
    {
        return $model->newQuery()
        ->with(['purchase.supplier'=> function($query){
            $query->select(['users.id','users.first_name']);
        },'purchase.warehouse'=> function($query){
            $query->select(['warehouses.id','warehouses.name']);
        },'purchase'])

        ->withCount(['purchase_return_items'])

        // ->select(['purchases.id','purchases.purchase_number','purchases.supplier_id','purchases.warehouse_id','purchases.order_date','purchases.delivery_date','purchases.total','purchases.status'])
        ->orderBy('purchase_returns.id','desc');

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
            ->addAction(['width' => '55px', 'class' => 'text-center', 'printable' => false, 'exportable' => false, 'title' => 'Action'])
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
            Column::make('purchase.purchase_number', 'purchase.purchase_number')->title(__('Purchase Number')),
            Column::make('purchase.supplier.first_name', 'purchase.supplier.first_name')->title(__('Supplier Name')),
            Column::make('purchase.warehouse.name', 'purchase.warehouse.name')->title(__('Warehouse')),
            Column::make('return_date', 'return_date')->title(__(' Date')),

            Column::make('total', 'total')->title(__('Total')),
            Column::make('total_product', 'total_product')->title(__('Total Product')),


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
        return 'PurchaseReturn_' . date('YmdHis');
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