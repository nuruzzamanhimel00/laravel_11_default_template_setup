<?php

namespace App\DataTables;

use PDF;
use App\Traits\DataTableTrait;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
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

            ->addColumn('action', function ($brand) {
                $buttons = '';
                if (auth()->user()->can('Edit Role')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('roles.edit', $brand->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
                }
                if (auth()->user()->can('Delete Role')) {
                    $buttons .= '<form action="' . route('roles.destroy', $brand->id) . '"  id="delete-form-' . $brand->id . '" method="post">
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
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('d M Y');
            })
            ->rawColumns(['action'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model->latest()->newQuery();
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
        return [
            [
                'defaultContent' => '',
                'data' => 'DT_RowIndex',
                'name' => 'DT_RowIndex',
                'title' => '#SL',
                'render' => null,
                'orderable' => false,
                'searchable' => false,
                'exportable' => false,
                'printable' => false,
                'footer' => '',
            ],
            [
                'title' => 'NAME',
                'name' => 'name',
                'data' => 'name'
            ],
            // [
            //     'title' => 'Created At',
            //     'name' => 'created_at',
            //     'data' => 'created_at'
            // ],
        ];

    }

    protected function getCustomFilename()
    {
        return 'Role_' . date('YmdHis');
    }

    public function pdf()
    {
        $excel = app('excel');
        $data = $this->getDataForExport();

        $pdf = PDF::loadView('vendor.datatables.print', [
            'data' => $data
        ]);
        return $pdf->download($this->getCustomFilename() . '.pdf');
    }

}
