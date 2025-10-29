<?php

namespace App\DataTables;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Promotion;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Services\DataTable;

class PromotionDataTable extends DataTable
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

                if (auth()->user()->can('Show Promotion')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('promotions.show', $item->id) . '" title="Edit"><i class="fa fa-eye"></i> ' . __('Show') . '</a></li>';
                }
                if (auth()->user()->can('Edit Promotion')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('promotions.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
                }
                if (auth()->user()->can('Delete Promotion')) {
                    $buttons .= '<form action="' . route('promotions.destroy', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
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
                return '<img class="ic-list-img" src="' . $item->image_url . '" alt="' . $item->title . '" />';
            })
            ->editColumn('status', function ($item) {
                $badge = $item->status == User::STATUS_ACTIVE ? "bg-success" : "bg-danger";
                return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
            })
            ->editColumn('applied_for', function ($item) {
                return ucwords($item->applied_for);
            })
            ->editColumn('message', function ($item) {
                return $item->message ?? '';
            })
            ->editColumn('start_date', function ($item) {
                return Carbon::parse($item->start_date)->format('F j, Y') ?? '';
            })
            ->editColumn('end_date', function ($item) {
                return Carbon::parse($item->end_date)->format('F j, Y') ?? '';
            })
            ->rawColumns(['image', 'status','created_at', 'action','message'])->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Promotion $model)
    {
        return $model->newQuery()

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
            Column::make('target_type', 'target_type')->title(__('Target Type')),
            Column::make('applied_for', 'applied_for')->title(__('Applied For')),
            Column::make('title', 'title')->title(__('Title')),

            // Column::make('message', 'message')->title(__('Message')),
            Column::make('start_date', 'start_date')->title(__('Start Date')),
            Column::make('end_date', 'end_date')->title(__('End Date')),
            Column::make('promotion_type', 'promotion_type')->title(__('Promotion Type')),
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
        return 'Promotions_' . date('YmdHis');
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
