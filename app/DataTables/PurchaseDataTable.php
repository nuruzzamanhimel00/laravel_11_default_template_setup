<?php

namespace App\DataTables;

use PDF;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class PurchaseDataTable extends DataTable
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
                if (auth()->user()->can('Show Purchase')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('purchases.show', $item->id) . '" title="Show Purchase"><i class="mdi mdi-eye mr-2"></i>  ' . __('Show') . '</a></li>';
                }
                if (auth()->user()->can('Edit Purchase') && $item->purchase_receives_count == 0 && $item->status != \App\Models\Purchase::STATUS_CANCEL) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('purchases.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> ' . __('Edit') . '</a></li>';
                }
                if (auth()->user()->can('Cancel Purchase')  && $item->purchase_receives_count == 0 && $item->status != \App\Models\Purchase::STATUS_CANCEL) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('purchases.cancel', $item->id) . '" title="Cancel"><i class="fa fa-times mt-1"></i> ' . __('Cancel') . '</a></li>';
                }
                if (auth()->user()->can('Add Receive Purchase') && $item->status != \App\Models\Purchase::STATUS_CANCEL &&
                $item->purchase_items->sum('receive_quantity') < $item->purchase_items->sum('quantity')
                ) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('purchases.receive', $item->id) . '" title="Receive"><i class="fa fa-arrow-circle-down mt-1"></i>' . __('Receive ') . '</a></li>';
                }

                if (auth()->user()->can('Add Return Purchase')  && $item->purchase_receives_count > 0 && $item->status != \App\Models\Purchase::STATUS_CANCEL) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('purchases.return', $item->id) . '" title="Return"><i class="fa fa-undo-alt mt-1"></i>' . __(' Return  ') . '</a></li>';
                }

                if (auth()->user()->can('Delete Purchase') && $item->purchase_receives_count == 0 && $item->status != \App\Models\Purchase::STATUS_CANCEL) {
                    $buttons .= '<form action="' . route('purchases.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $item->id . ')" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
                    ';
                }
                return '<div class="btn-group dropstart">
                              <button class="btn btn-secondary dropdown-toggle"  type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                              </button>
                              <ul class="dropdown-menu" role="menu" >
                              '. $buttons .'
                              </ul>
                        </div>';



            })


            ->editColumn('status', function ($item) {
                $badge = $item->status == Purchase::STATUS_REQUESTED ? "bg-info" : ( $item->status == Purchase::STATUS_CANCEL ? "bg-danger": "bg-success") ;

                return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
            })
            ->editColumn('total', function ($item) {
                return addCurrency($item->total);

            })

            ->addColumn('total_product', function ($item) {
                return $item->purchase_items->count();
            })
            // ->addColumn('supplier_name', function ($item) {
            //     return $item->supplier->supplier_name ?? null;
            // })
            ->addColumn('missing_item', function ($item) {
                $total_purchase_item_qty = $item->purchase_items->sum('quantity');
                $total_receive_item_qty = $item->purchase_items->reduce(function (?int $carry, $item) {
                    return $carry + ($item->purchase_receive_items->isNotEmpty() ? $item->purchase_receive_items->sum('quantity') : 0);
                }, 0);
                $total = $total_purchase_item_qty - $total_receive_item_qty;
                if($total_receive_item_qty && $total_receive_item_qty > 0 && (int)($total) > 0){
                    return '<span class="badge bg-danger">Missing (' . $total_purchase_item_qty - $total_receive_item_qty . ')</span>';
                }

            })

            ->rawColumns([ 'status','created_at', 'action','missing_item'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Purchase $model)
    {
        return $model->newQuery()
        ->with(['supplier'=> function($query){
            $query->select(['users.id','users.first_name','users.email','users.email','users.phone','users.type']);
        },'warehouse'=> function($query){
            $query->select(['warehouses.id','warehouses.name']);
        },'purchase_items.purchase_receive_items','supplier.supplier'])

        ->withCount(['purchase_receives'])

        // ->select(['purchases.id','purchases.purchase_number','purchases.supplier_id','purchases.warehouse_id','purchases.order_date','purchases.delivery_date','purchases.total','purchases.status'])
        ->orderBy('purchases.id','desc');

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
            Column::make('purchase_number', 'purchase_number')->title(__('Purchase Number')),
            Column::make('supplier.supplier.company', 'supplier.supplier.company')->title(__('Company')),
            // Column::make('supplier.first_name', 'supplier.first_name')->title(__('Supplier Name')),
            Column::make('warehouse.name', 'warehouse.name')->title(__('Warehouse')),
            Column::make('date', 'date')->title(__('Date')),
            Column::make('total', 'total')->title(__('Total')),
            Column::make('total_product', 'total_product')->title(__('Total Product')),
            Column::make('status', 'status')->title(__('Status')),
            Column::make('missing_item', 'missing_item')->title(__('Missing Item')),

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
        return 'Purchases_' . date('YmdHis');
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
