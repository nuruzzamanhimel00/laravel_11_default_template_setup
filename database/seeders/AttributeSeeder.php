<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\AttributeItem;
use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allData = [
            [
                'name' => 'Size',
                'attribute_items' =>[
                    "Small",
                    "Medium",
                    "Large",
                ]
            ],
            [
                'name' => 'Packaging',
                'attribute_items' =>[
                    "Loose",
                    "Pre-packaged",

                ]
            ],
            [
                'name' => 'Ripeness',
                'attribute_items' =>[
                    "Unripe",
                    "Semi-ripe",
                    "Fully ripe",

                ]
            ],
            [
                'name' => 'Type',
                'attribute_items' =>[
                    "Organic",
                    "Conventional",
                ]
            ],


        ];
        foreach($allData as $data) {
            // $Attribute = Attribute::updateOrCreate([
            //     'name' => $data['name'],
            // ],[
            //     'name' => $data['name']
            // ]);
            $attribute =ProductAttribute::firstOrCreate([
                'name' => $data['name'],
            ],[
                'name' => $data['name'],
                'status' =>STATUS_ACTIVE,
            ]);
            if($attribute){
                foreach($data['attribute_items'] as $item_name){
                    $attribute->values()->updateOrCreate([
                        'value' => $item_name,
                    ]);
                    // AttributeItem::updateOrCreate([
                    //     'attribute_id' => $Attribute->id,
                    //     'name' => $item_name
                    // ],[
                    //     'attribute_id' => $Attribute->id,
                    //     'name' => $item_name
                    // ]
                    // );
                }

            }

        }


    }
}
