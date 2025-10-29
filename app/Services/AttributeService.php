<?php

namespace App\Services;

use App\Models\User;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Constraint\Count;

class AttributeService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(ProductAttribute $model)
    {
        parent::__construct($model);
    }
    public function createOrUpdate(Request|array $request, int $id = null): ProductAttribute
    {
        $data = $request->all();
        $attribute_data = $request->only(['name', 'status']);

        try {
            DB::beginTransaction();

            // Determine if it's an update or create operation
            $attribute = $id ? $this->get($id)->load('values') : $this->model::create($attribute_data);

            // Process item data
            if (!empty($data['item_data'])) {
                $data['item_data'] = $this->processItemData($data['item_data'], $request);

                // If updating, delete existing values
                if ($id) {
                    $attribute->values()->delete();
                }

                // Create new values
                $attribute->values()->createMany($data['item_data']);
            }

            // Update only for existing records
            if ($id) {
                $attribute->update($attribute_data);
            }

            DB::commit();
            return $attribute;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Process item data and handle file uploads.
     */
    private function processItemData(array $itemData, Request $request): array
    {
        foreach ($itemData as $key => $value) {
            $itemData[$key]['image'] = isset($value['image']) && $value['image'] !== null
                ? $this->fileUploadService->uploadFile(
                    $request,
                    "item_data.$key.image",
                    ProductAttributeValue::FILE_STORE_PATH,
                    $value['old_image'] ?? ''
                )
                : ($value['old_image'] ?? '');
        }
        return $itemData;
    }

    public function delete($id){
        $attribute = $this->get($id);

        try {
            DB::beginTransaction();
                $attribute->values()->delete();

                $attribute->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;
        }

    }

    // create or update attribute value
    public function createOrUpdateAttributeValue(Request|array $data, ProductAttribute $attribute)
    {
        foreach ($data as $key => $value) {

            $id = isset($value['id']) ? $value['id'] : null;
            try {
                DB::beginTransaction();
                if ($id) {
                    // Update
                    $attribute_item           = $attribute->values()->find($id);
                    $attribute_item->value    = $data['name'];
                    $attribute_item->save();
                    DB::commit();
                } else {
                    // Create
                    $attribute_item                       = $attribute->values()->create([
                        'value' => $data['name']
                    ]);
                    DB::commit();
                    return $attribute_item;
                }

            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }
    }

}
