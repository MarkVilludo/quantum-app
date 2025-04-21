<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'module_id' => $this->module_id,
            'data' => $this->data,
            'activity' => $this->activity,
            'is_unlocked' => $this->is_unlocked ?? false,
        ];
    }
}
