<?php

namespace App\DataTables;

use PDF;
use App\Models\Brand;
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

class BrandDataTable extends DataTable
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
        ->addColumn('action', function ($brand) {
            $buttons = '';
            if (auth()->user()->can('Edit Brand')) {
                $buttons .= '<li><a class="dropdown-item" href="' . route('brands.edit', $brand->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
            }
            if (auth()->user()->can('Delete Brand')) {
                $buttons .= '<form action="' . route('brands.destroy', $brand->id) . '"  id="delete-form-' . $brand->id . '" method="post">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <input type="hidden" name="_method" value="DELETE">
                <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $brand->id . ')" data-from-name="'. $brand->name.'" data-from-id="' . $brand->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
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
        ->editColumn('image', function ($brand) {
            return '<img class="ic-list-img" src="' . getStorageImage($brand->image, false) . '" alt="' . $brand->name . '" />';
        })
        // ->editColumn('created_at', function ($role) {
        //     return $role->created_at->format('d M Y h:i A');
        // })
        ->editColumn('status', function ($item) {
            $badge = $item->status == STATUS_ACTIVE ? "bg-success" : "bg-danger";
            return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
        })
        ->rawColumns(['status','created_at', 'image', 'action','status'])->addIndexColumn();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Brand $model): QueryBuilder
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
            Column::make('status', 'status')->title(__('Status')),
            // Column::make('created_at', 'created_at')->title(__('Created At')),
        ];
        if (!request()->has('action')) {
            array_splice($columns, 1, 0, [
                Column::make('image', 'image')->title(__('Image')),
            ]);
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Brand_' . date('YmdHis');
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
