<?php

namespace App\Http\Resources\Promotion;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Promotion\PromotionItemsResource;

class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        // return [
        //     'id' => $this->id,
        //     'title' => $this->title ?? '',
        //     'message' => $this->message ?? '',
        //     'image' => $this->image ?? null,
        //     'image_url' => $this->image_url ?? null,
        //     'start_date' => $this->start_date ?? '',
        //     'end_date' => $this->end_date ?? '',
        //     'target_type' => $this->target_type ?? '',
        //     'valid_from' => $this->valid_from ?? '',
        //     'valid_to' => $this->valid_to ?? '',
        //     'applied_for' => $this->applied_for ?? '',
        //     'offer_type' => $this->offer_type ?? '',
        //     'offer_value' => $this->offer_value ?? '',
        //     'in_homepage' => $this->in_homepage ?? '',
        //     'promotion_type' => $this->promotion_type ?? '',
        //     'promotion_items' => PromotionItemsResource::collection($this->whenLoaded('promotion_items')),
        // ];

        $data = [
            'id' => $this->id,
            'title' => $this->title ?? '',
            // 'image' => $this->image ?? null,
            'image_url' => $this->image_url ?? null,
        ];

        if (!is_null($this->message)) {
            $data['description'] = $this->message;
        }
        // if (!is_null($this->start_date)) {
        //     $data['start_date'] = $this->start_date;
        // }

        // if (!is_null($this->end_date)) {
        //     $data['end_date'] = $this->end_date;
        // }

        if (!is_null($this->end_date)) {
            $data['end_date'] = !empty($this->end_date) ? Carbon::parse($this->end_date)->format('F j, Y') : null;
        }

        if (!is_null($this->target_type)) {
            $data['target_type'] = $this->target_type;
        }

        // if (!is_null($this->valid_from)) {
        //     $data['valid_from'] = $this->valid_from;
        // }

        // if (!is_null($this->valid_to)) {
        //     $data['valid_to'] = $this->valid_to;
        // }

        if (!is_null($this->applied_for)) {
            $data['applied_for'] = $this->applied_for;
        }

        if (!is_null($this->offer_type)) {
            $data['offer_type'] = $this->offer_type;
        }

        if (!is_null($this->offer_value)) {
            $data['offer_value'] = $this->offer_value;
        }

        if (!is_null($this->in_homepage)) {
            $data['in_homepage'] = $this->in_homepage;
        }

        // if (!is_null($this->promotion_type)) {
        //     $data['promotion_type'] = $this->promotion_type;
        // }

        if ($this->relationLoaded('promotion_items')) {
            $data['promotion_items'] = PromotionItemsResource::collection($this->promotion_items);
        }

        return $data;

    }
}
