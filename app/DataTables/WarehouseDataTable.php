<?php

namespace App\DataTables;

use PDF;
use App\Models\Warehouse;
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

class WarehouseDataTable extends DataTable
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
        ->addColumn('action', function ($warehouse) {
            $buttons = '';
            if (auth()->user()->can('Show Warehouse')) {
                $buttons .= '<li><a class="dropdown-item" href="' . route('warehouses.show', $warehouse->id) . '" title="Edit"><i class="fa fa-eye"></i> ' . __('Show') . '</a></li>';
            }
            if (auth()->user()->can('Edit Warehouse')) {
                $buttons .= '<li><a class="dropdown-item" href="' . route('warehouses.edit', $warehouse->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
            }
            if (auth()->user()->can('Delete Warehouse')) {
                $buttons .= '<form action="' . route('warehouses.destroy', $warehouse->id) . '"  id="delete-form-' . $warehouse->id . '" method="post">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <input type="hidden" name="_method" value="DELETE">
                <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $warehouse->id . ')" data-from-name="'. $warehouse->name.'" data-from-id="' . $warehouse->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
                ';
            }

            return '<div class="btn-group dropstart">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                          '. $buttons .'
                          </ul>
                    </div>';
        })
        ->editColumn('is_default', function ($warehouse) {
            $is_default = $warehouse->is_default == 1 ? 'Yes' : 'No';
            return $is_default;
        })
        ->editColumn('created_at', function ($warehouse) {
            return $warehouse->created_at->format('d M Y h:i A');
        })
        ->editColumn('status', function ($item) {
            $badge = $item->status == STATUS_ACTIVE ? "bg-success" : "bg-danger";
            return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
        })
        ->rawColumns(['status','created_at', 'is_default', 'action'])->addIndexColumn();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Warehouse $model): QueryBuilder
    {
        return $model->latest()->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
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
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns =  [
            Column::computed('DT_RowIndex', __('SL')),
            Column::make('name', 'name')->title(__('Name')),
            Column::make('phone', 'phone')->title(__('Phone')),
            Column::make('status', 'status')->title(__('Status')),
            Column::make('is_default', 'is_default')->title(__('Is Default')),
            // Column::make('created_at', 'created_at')->title(__('Created At')),
        ];

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Warehouse_' . date('YmdHis');
    }

    public function pdf()
    {
        $data = $this->getDataForExport();

        $pdf = PDF::loadView('vendor.datatables.print', [
            'data' => $data
        ]);
        return $pdf->download($this->getFilename() . '.pdf');
    }
}
