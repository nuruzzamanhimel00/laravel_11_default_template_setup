<?php

namespace App\DataTables;

use PDF;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class CategoryDataTable extends DataTable
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

            ->addColumn('action', function ($item) {
                $buttons = '';

                if (auth()->user()->can('Edit Category')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('categories.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
                }
                if (auth()->user()->can('Delete Category')) {
                    $buttons .= '<form action="' . route('categories.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $item->id . ')" data-from-name="'. $item->name.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
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
            ->editColumn('image', function ($item) {
                $html = '<img class="img-64" src="'.$item->image_url.'" alt="Computer">';
                return $html;
            })
            ->editColumn('status', function ($item) {
                $badge = $item->status == User::STATUS_ACTIVE ? "bg-success" : "bg-danger";
                return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
            })
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('d M Y h:i A');
            })
            ->addColumn('parent_name', function ($item) {
                return !is_null($item->parent) ? $item->parent->name : '';
            })

            ->rawColumns(['avatar', 'status','created_at', 'action','image'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
        return $model->newQuery()
        ->with(['parent'])
        ->latest();

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
    public function getColumns(): array
    {
        $columns =  [
            Column::computed('DT_RowIndex', __('SL')),
            Column::make('name', 'name')->title(__('Name')),
            Column::make('parent_name', 'parent_name')->title(__('Patent Category')),
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
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Category_' . date('YmdHis');
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
