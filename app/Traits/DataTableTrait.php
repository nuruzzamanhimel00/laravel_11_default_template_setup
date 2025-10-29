<?php


namespace App\Traits;

use Yajra\DataTables\Html\Button;




trait DataTableTrait
{
    public function getDynamicDataTableButtons($createRoute = null, $customBtn = [])
    {
        // Set the default route if no route is provided
        $createRoute = $createRoute ?? null;

        $buttons = [];

        if (!is_null($createRoute)) {
            $buttons = [
                Button::make('create')->action($this->createNewItem($createRoute)),
            ];
        }

        // Check if export is enabled

        $buttons = array_merge($buttons, [
            Button::make('excel')->text('<i class="far fa-file-excel"></i> <span class="ic_text_table_btn">Excel</span>'),
            Button::make('csv')->text('<i class="fas fa-file-csv"></i> <span class="ic_text_table_btn">CSV</span>'),
            Button::make('pdf')->text('<i class="fas fa-file-pdf"></i> <span class="ic_text_table_btn">PDF</span>'),
            Button::make('print')->text('<i class="fas fa-print"></i> <span class="ic_text_table_btn">Print</span>'),
        ]);


        if (count($customBtn) > 0) {
            $buttons = array_merge($buttons, $customBtn);
        }

        return $buttons;
    }



          /**
     * Create a JavaScript action for redirecting to the create new item page.
     *
     * @return string
     */
    protected function createNewItem($createRoute): string
    {
        return "window.location = '" . $createRoute . "';";
    }
}
