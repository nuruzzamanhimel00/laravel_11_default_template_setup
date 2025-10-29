<?php

namespace App\DataTables;

use PDF;
use App\Models\User;
use App\Models\Brand;
use App\Models\Review;
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

class ProductReviewsDataTable extends DataTable
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
        ->addColumn('action', function ($item) {
            $buttons = '';
            if (auth()->user()->can('Reviews Edit Product')) {
                $buttons .= '<li><a class="dropdown-item" href="' . route('product.reviews.edit', $item->id) . '" title="Edit"><i class="mdi mdi-square-edit-outline"></i>' . __('Edit') . '</a></li>';
            }
            if (auth()->user()->can('Reviews Delete Product')) {
                $buttons .= '<form action="' . route('product.reviews.delete', $item->id) . '"  id="delete-form-' . $item->id . '" method="post">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <input type="hidden" name="_method" value="DELETE">
                <button class="dropdown-item text-danger delete-list-data" onclick="return makeDeleteRequest(event, ' . $item->id . ')" data-from-name="'. $item->rating.'" data-from-id="' . $item->id . '"   type="button" title="Delete"><i class="mdi mdi-trash-can-outline"></i> ' . __('Delete') . '</button></form>
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
        ->editColumn('message', function ($item) {
            $html = '
            <div class="text-wrap text-break" style="
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            ">
                ' . $item->message . '
            </div>
            ';
            return $html;
        })
        ->editColumn('images', function ($item) {
            if ($item->images->isEmpty()) {
                return 'No files';
            }

            $firstImage = $item->images->first();
            $imageCount = $item->images->count();
            $galleryId = 'gallery_'.$item->id;

            // Generate additional images HTML (skip first one as it's handled separately)
            $additionalImages = $item->images->slice(1)->map(function ($image) use ($galleryId) {
                return <<<HTML
                    <a data-fancybox="{$galleryId}" href="{$image->image_url}">
                        <img src="{$image->image_url}" />
                    </a>
                HTML;
            })->implode(' ');

            return <<<HTML
                <a data-fancybox="{$galleryId}" href="{$firstImage->image_url}">
                    Have {$imageCount} files
                </a>
                <div style="display:none">
                    {$additionalImages}
                </div>
            HTML;
        })
        // ->editColumn('created_at', function ($role) {
        //     return $role->created_at->format('d M Y h:i A');
        // })
        ->editColumn('status', function ($item) {
            $badge = $item->status == STATUS_ACTIVE ? "bg-success" : "bg-danger";
            return '<span class="badge ' . $badge . '">' . Str::upper($item->status) . '</span>';
        })
        ->editColumn('customer.first_name', function ($item) {
            $customerType = $item->customer->type == User::TYPE_REGULAR_USER ? 'Home User' : User::TYPE_RESTAURANT;
            return ucwords($item->customer->first_name) . ' (' . $customerType . ')';
        })
        ->rawColumns(['status','created_at', 'images', 'action','status','message'])->addIndexColumn();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Review $model): QueryBuilder
    {
        $productId = request()->route('id'); // ✅ get {id} from the route
        // dd($productId); // test it
        return $model->latest()
        ->where('product_id', $productId)
        ->with([
            'customer:id,first_name,last_name,type',
            'images:id,image,review_id',
        ])
        ->newQuery();
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
            Column::make('customer.first_name', 'customer.first_name')->title(__('Home User/Restaurant Name')),
            Column::make('rating', 'rating')->title(__('Rating')),
            Column::make('message', 'message')->title(__('Message')),

            Column::make('status', 'status')->title(__('Status')),
            // Column::make('created_at', 'created_at')->title(__('Created At')),
        ];
        if (!request()->has('action')) {
            array_splice($columns, 3, 0, [
                Column::make('images', 'images')->title(__('Images')),
            ]);
        }

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductReview_' . date('YmdHis');
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
