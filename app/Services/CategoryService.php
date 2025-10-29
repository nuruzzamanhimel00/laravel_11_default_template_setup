<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\InvestorPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CategoryService extends BaseService
{
//    protected $fileUploadService;

    public function __construct(Category $model)
    {
        parent::__construct($model);
//        $this->fileUploadService = $fileUploadService;
    }
    public function createOrUpdate(Request|array $request, int $id = null): Category
    {
        $data           = $request->all();

        try {
            DB::beginTransaction();
            // dd($data);

            if ($id) {

                $data['updated_by'] = Auth::id();


                $item = $this->model::updateOrCreate([
                    'id' => $id
                ],$data);

                // dd($item);

                    // item
                if (isset($data['image']) && $data['image'] != null) {
                    $item->image = $this->fileUploadService->uploadFile($request,'image',Category::FILE_STORE_PATH,$item->image);
                }

                 $item->save();


                DB::commit();
                return $item;
            } else {

                $data['created_by'] = Auth::id();
                $data['slug'] = Str::slug($data['name']);


                if (isset($data['image']) && $data['image'] != null) {
                    $data['image']      = $this->fileUploadService->uploadFile($request,'image',Category::FILE_STORE_PATH);
                }
                // dd($data);
                //category create
                $date                       = $this->model::create($data);
                DB::commit();
                return $date;

            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }


    public function delete($id)
    {
        $item = $this->get($id)->load('childs');

        // Delete the main item's image if it exists
        $this->deleteImage($item->image);

        // Delete child items and their images
        $item->childs->each(function ($child) {
            $this->deleteImage($child->image);
            $child->delete();
        });

        // Delete the main item
        $item->delete();
    }

    private function deleteImage($image)
    {
        if (!is_null($image)) {
            $this->fileUploadService->delete($image);
        }
    }

    public function parentCategory(){
        return $this->model::where('parent_id',null)->with(['childs'])
        ->where('status',STATUS_ACTIVE)
        ->get();
    }

    public function getActive($selectedFields = ['id', 'parent_id', 'name', 'slug', 'image', 'description', 'status'])
    {
        // $availableFor = request()->get('available_for'); // 'Restaurant' or 'Customer'
        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
        $isHomePage = request()->get('in_homepage', false);
        if($isHomePage == 1){
            $perPage = request()->get('per_page', 8);
        }else{
            $perPage = request()->get('per_page', 20);
        }

        return $this->model
            ->active()
            ->select($selectedFields)
            // ->with(['parent' => function ($query) use($selectedFields) {
            //     $query->select($selectedFields);
            // },'childs' => function ($query) use($selectedFields) {
            //     $query->select($selectedFields);
            // }])
            // ->when($availableFor, function ($query) {
            //     $query->whereHas('products', function ($query) {
            //         $query->availableFor();
            //     });
            // })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }
    public function getCategory($slugOrId)
    {
        // $availableFor = request()->get('available_for'); // 'Restaurant' or 'Customer'
        $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
        try {
            return $this->model->active()
            ->select(['id', 'parent_id', 'name', 'slug', 'image', 'description', 'status'])
            ->with(['parent:id,parent_id,name,slug,image,description,status'])
            ->where('slug', $slugOrId)->orWhere('id', $slugOrId)
            // ->when($availableFor, function ($query) {
            //     $query->whereHas('products', function ($query) {
            //         $query->availableFor();
            //     });
            // })
            ->first();
        } catch (\Exception $e) {
            logger($e->getMessage());
            return false;
        }
    }

    public function getSubCategories($selectedFields = ['id', 'parent_id', 'name', 'slug', 'image', 'description', 'status'],$id)
    {

        $perPage = request()->get('per_page', 20);
        return $this->model
            ->where('parent_id', $id)
            ->active()
            ->select($selectedFields)

            ->orderBy('position', 'asc')
            ->paginate($perPage);
    }

    public function getActiveParentCategories($selectedFields = ['id', 'parent_id', 'name', 'slug', 'image', 'description', 'status'])
    {

        // $availableFor = auth('api')->check() ? auth('api')->user()->type: null;
        $isHomePage = request()->get('in_homepage', false);
        if($isHomePage == 1){
            $perPage = request()->get('per_page', 8);
        }else{
            $perPage = request()->get('per_page', 20);
        }

        return $this->model
            ->active()
            ->select($selectedFields)
            ->parent()
            // ->with(['parent' => function ($query) use($selectedFields) {
            //     $query->select($selectedFields);
            // },'childs' => function ($query) use($selectedFields) {
            //     $query->select($selectedFields);
            // }])
            // ->when($availableFor, function ($query) {
            //     $query->whereHas('products', function ($query) {
            //         $query->availableFor();
            //     });
            // })
            ->orderBy('position', 'asc')
            ->paginate($perPage);
    }

}
