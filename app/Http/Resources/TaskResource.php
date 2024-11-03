<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->formatDate($this->due_date),
            'priority' => [
                'id' => $this->priority_id,
                'name' => $this->priority->name ?? 'Normal',
            ],
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'completed_at' => $this->formatDate($this->completed_at),
            'archived_at' => $this->formatDate($this->archived_at),
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }

    /**
     * Format the date to a more readable format or return null if not set.
     *
     * @param  string|null  $date
     * @return string|null
     */
    private function formatDate($date)
    {
        return $date ? Carbon::parse($date)->toDateTimeString() : null;
    }
}
