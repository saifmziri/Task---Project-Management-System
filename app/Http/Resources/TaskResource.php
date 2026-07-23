<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
            return [
            'id' => $this->id,
            'task_name' => $this->task_name,
            'project_id' => $this->project_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'user_name' => $this->whenLoaded('user', fn () => $this->user->full_name),
        ];
    }
}
