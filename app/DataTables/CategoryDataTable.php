<?php

namespace App\DataTables;

use PDF;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Traits\DataTableTrait;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategoryDataTable extends DataTable
{
    use DataTableTrait;

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($item) {
                $buttons = '';

                if (auth()->user()->can('Edit Category')) {
                    $buttons .= '<li><a class="dropdown-item" href="' . route('categories.edit', $item->id) . '">' . __('Edit') . '</a></li>';
                }

                if (auth()->user()->can('Delete Category')) {
                    $buttons .= '<form action="' . route('categories.destroy', $item->id) . '" method="POST" id="delete-form-'.$item->id.'">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" onclick="return makeDeleteRequest(event, '.$item->id.')" class="dropdown-item text-danger">'.__('Delete').'</button>
                    </form>';
                }

                return '<div class="btn-group dropstart">
                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                    <ul class="dropdown-menu">'.$buttons.'</ul>
                </div>';
            })
            ->editColumn('image', fn($item) => '<img class="img-64" src="'.$item->image_url.'" alt="Image">')
            ->editColumn('status', fn($item) => '<span class="badge '.($item->status == User::STATUS_ACTIVE ? 'bg-success' : 'bg-danger').'">'.Str::upper($item->status).'</span>')
            ->editColumn('created_at', fn($item) => $item->created_at->format('d M Y h:i A'))
            ->addColumn('sub_category', function ($item) {
                if ($item->childs->isEmpty()) return '-';

                $html = '<div class="sub-category-container">
                    <a href="javascript:void(0)" class="toggle-subcategory-show" data-id="'.$item->id.'" id="show-btn-'.$item->id.'">
                        <i class="fas fa-chevron-down"></i> Show
                    </a>
                    <a href="javascript:void(0)" class="toggle-subcategory-hide d-none" data-id="'.$item->id.'" id="hide-btn-'.$item->id.'">
                        <i class="fas fa-chevron-up"></i> Hide
                    </a>
                    <ul class="sub-category-list mt-2" id="sub-category-'.$item->id.'" data-category-id="'.$item->id.'" style="display:none;">';

                foreach ($item->childs as $child) {
                    $editUrl = route('categories.edit', $child->id);
                    $deleteUrl = route('categories.destroy', $child->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    $html .= '<li class="sortable-subcategory-item d-flex justify-content-between align-items-center" data-id="'.$child->id.'" style="cursor: move;">
                        <span>
                        <a href="'.$child->image_url.'" draggable="false" target="_blank"><img class="" src="'.$child->image_url.'"  alt="Image" draggable="false" width="20" height="20" style="
                        margin-right: 7px;
                    "></a>
                        '.e($child->name).'</span>
                        <div class="btn-group">
                            <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary" title="Edit"><i class="mdi mdi-square-edit-outline"></i></a>
                            <form method="POST" action="'.$deleteUrl.'" id="delete-form-'.$child->id.'" >
                                '.$csrf.$method.'
                                <button type="submit" onclick="return makeDeleteRequest(event, '.$child->id.')"  class="btn btn-sm btn-outline-danger" title="Delete"><i class="mdi mdi-trash-can-outline"></i></button>
                            </form>
                        </div>
                    </li>';
                }

                $html .= '</ul></div>';
                return $html;
            })

            ->setRowAttr(['data-id' => fn($item) => $item->id])
            ->rawColumns(['status', 'created_at', 'action', 'image', 'sub_category'])
            ->addIndexColumn();
    }

    public function query(Category $model)
    {
        return $model->newQuery()
            ->whereNull('parent_id') // only parent categories
            ->with(['childs' => fn($query) => $query->orderBy('position')])
            ->orderBy('position');
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title' => 'Action', 'printable' => false, 'exportable' => false])
            ->parameters([
                'order' => [[2, 'asc']],
                'lengthChange' => true,
                'lengthMenu' => [[10, 20, 50, -1], [10, 20, 50, "All"]],
                'pageLength' => 10,
            ])
            ->buttons(...$this->getDynamicDataTableButtons(null));
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('SL')),
            Column::make('name')->title(__('Name')),
            Column::make('sub_category')->title(__('Sub Category')),
            Column::make('status')->title(__('Status')),
            Column::make('image')->title(__('Image')),
        ];
    }

    protected function filename(): string
    {
        return 'Category_' . date('YmdHis');
    }

    public function pdf()
    {
        $pdf = PDF::loadView('vendor.datatables.print', ['data' => $this->getDataForExport()]);
        return $pdf->download($this->getFilename().'.pdf');
    }
}
