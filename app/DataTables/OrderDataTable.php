<?php

namespace App\DataTables;

use PDF;
use App\Models\Sale;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class OrderDataTable extends DataTable
{
    use DataTableTrait;
    public $currency;
    public function __construct(){
        $this->currency = getCurrency();
    }
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
                if (auth()->user()->can('Show Order')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('orders.show', $item->id) . '" title="Edit"><i class="mdi mdi-eye mr-2"></i> ' . __('Show') . '</a></li>';
                }
                if (auth()->user()->can('Edit Order')&& $item->platform == Order::PLATFORM_ADMIN  && $item->order_status == Order::STATUS_PENDING  && $item->order_status != Order::STATUS_CANCEL) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('orders.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i> ' . __('Edit') . '</a></li>';
                }
                // if (auth()->user()->can('Cancel Order')  && $item->status != Order::STATUS_CANCEL && $item->status != Order::STATUS_PENDING) {
                //     $buttons .= '<li><a class="dropdown-item" href="' . route('order.cancel', $item->id) . '" title="Cancel"><i class="fa fa-times mt-1"></i> ' . __('Cancel') . '</a></li>';
                // }
                if (auth()->user()->can('Make Payment Order') && $item->total > $item->total_paid && $item->status != Order::STATUS_CANCEL) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('order.payment', $item->id) . '" title="Make Payment"><i class="mdi mdi-cash"></i> ' . __('Make Payment ') . '</a></li>';
                }
                if (auth()->user()->can('View Payment Order') ) {
                    $buttons .= '<li><a class="dropdown-item list_payment_btn" data-id="'.$item->id.'" href="#" title="View Payment"><i class="mdi mdi-cash-multiple"></i> ' . __('View Payment') . '</a></li>';
                    }

                if (auth()->user()->can('Status Change Order') ) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('order.status.change', $item->id) . '" title="Status Change"><i class="fas fa-pencil-alt"></i> ' . __('Status Change ') . '</a></li>';
                }
                if (auth()->user()->can('Histories Order') ) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('order.histories', $item->id) . '" title="Status Change"><i class="fas fa-history"></i>  ' . __('Order Histories ') . '</a></li>';
                }


                if (auth()->user()->can('Delete Order') && $item->order_status == Order::STATUS_PENDING) {
                    $buttons .= '<form action="' . route('orders.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $item->id . ')" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
                    ';
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


            ->editColumn('order_status', function ($item) {

                return $item->order_status_html;
            })
            ->editColumn('platform', function ($item) {
                return $item->platform_html;
            })

            ->editColumn('payment_status', function ($item) {

                return $item->payment_status_html;
            })
            ->editColumn('delivery_status', function ($item) {

                return $item->delivery_status_html;
            })
            // ->editColumn('total', function ($item) {
            //     return '$ ' . $item->total;
            // })
            // ->editColumn('total_paid', function ($item) {
            //     return '$ ' . $item->total_paid;
            // })


            ->rawColumns([ 'order_status','created_at', 'action','platform','payment_status','delivery_status'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model->newQuery()
        ->with([

        'customer'=> function($query){
            $query->select(['users.id','users.first_name','users.last_name']);
        },

        ])
        ->select(['orders.id','orders.date','orders.invoice_no','orders.total','orders.created_at','orders.order_for_id','orders.order_for','orders.total_paid','orders.order_status','orders.delivery_status','orders.payment_status','orders.platform'])

        ->withCount(['order_payments'])

        ->orderBy('orders.id','desc');

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
        // $customBtn = [
        //     Button::make()
        //     ->text('Import')
        //     ->addClass('dt-button buttons-excel ')
        //     ->attr([
        //         'data-bs-toggle' => 'modal', // Add the id here
        //         'data-bs-target' => '#import_csv_modal'

        //     ]),

        // ];
        $buttons = $this->getDynamicDataTableButtons(null,);
        // dd($buttons);
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
            Column::make('invoice_no', 'invoice_no')->title(__('Invoice No')),
            Column::make('date', 'date')->title(__('Date')),
            Column::make('platform', 'platform')->title(__('Platform')),
            Column::make('customer.first_name', 'customer.first_name')->title(__('Customer')),
            Column::make('total', 'total')->title(__('Total'.' ('.$this->currency.')')),
            Column::make('total_paid', 'total_paid')->title(__('Total Paid'.' ('.$this->currency.')')),
            Column::make('order_status', 'order_status')->title(__('Order Status')),
            Column::make('delivery_status', 'delivery_status')->title(__('Delivery Status')),
            Column::make('payment_status', 'payment_status')->title(__('Payment Status')),


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
        return 'Orders_' . date('YmdHis');
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
