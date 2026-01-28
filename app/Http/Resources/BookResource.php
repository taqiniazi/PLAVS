<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'publisher' => $this->publisher,
            'published_year' => $this->published_year,
            'description' => $this->description,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'status' => $this->status,
            'owner' => $this->owner,
            'library' => $this->shelf && $this->shelf->room && $this->shelf->room->library 
                ? [
                    'id' => $this->shelf->room->library->id,
                    'name' => $this->shelf->room->library->name,
                  ] 
                : null,
            'shelf' => $this->shelf ? [
                'id' => $this->shelf->id,
                'name' => $this->shelf->name,
                'room' => $this->shelf->room ? $this->shelf->room->name : null,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
