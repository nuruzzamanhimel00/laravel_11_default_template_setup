<?php

namespace App\DataTables;

use PDF;
use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class AgentDataTable extends DataTable
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
                if (auth()->user()->can('Show Agent')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('agents.show', $item->id) . '" title="Edit"><i class="fa fa-eye"></i> ' . __('Show') . '</a></li>';
                }
                if (auth()->user()->can('Edit Agent')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('agents.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
                }

                if (auth()->user()->can('Delete Agent') ) {
                    $buttons .= '<form action="' . route('agents.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
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

            ->editColumn('avatar', function ($item) {
                return '<img class="ic-list-img" src="' . getStorageImage($item->avatar,true) . '" alt="' . $item->name . '" />';
            })
            // ->editColumn('status_switch', function ($item) {

            //     $checked = $item->status == User::STATUS_ACTIVE ? 'checked' : '';
            //     return '
            //         <div class="form-check form-switch">
            //             <input class="form-check-input checkbox_switch" type="checkbox" ' . $checked . ' data-id="' . $item->id . '">
            //         </div>
            //     ';
            // })
            ->editColumn('status', function ($item) {
                $badge = $item->status == STATUS_ACTIVE ? "bg-success" : "bg-danger";
                $status = $item->status == STATUS_ACTIVE ? "Active" : "Inactive";
                return '<span class="badge ' . $badge . '">' . Str::upper($status) . '</span>';
            })
            ->editColumn('created_at', function ($role) {
                return $role->created_at->format('d M Y h:i A');
            })->rawColumns(['avatar', 'status_switch','created_at', 'action','status'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        return $model->newQuery()
        ->where('type',User::TYPE_AGENT)
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
    protected function getColumns()
    {

        $columns = [
            Column::computed('DT_RowIndex', __('SL')),
            Column::make('first_name', 'first_name')->title(__(' Name')),
            // Column::make('last_name', 'last_name')->title(__('Last Name')),
            Column::make('email', 'email')->title(__('Email')),
            Column::make('phone', 'phone')->title(__('Phone')),

            Column::make('status', 'status')->title(__('Active/Inactive')),
            // Column::make('created_at', 'created_at')->title(__('Created At')),
        ];
        if (!request()->has('action')) {
            array_splice($columns, 1, 0, [
                Column::make('avatar', 'avatar')->title(__('Image')),
            ]);

        }
        // if (request()->has('action')) {
        //     array_splice($columns, 4, 0, [
        //         Column::make('status', 'status')->title(__('Approve/Reject')),
        //     ]);
        // }

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Agents_' . date('YmdHis');
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
