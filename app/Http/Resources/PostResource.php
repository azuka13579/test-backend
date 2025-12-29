<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{

    $imageUrl = null;



        if ($this->image_url) {
            // Cek apakah string dimulai dengan "http" (berarti link luar/seeder)
            if (Str::startsWith($this->image_url, 'http')) {
                $imageUrl = $this->image_url;
            } else {
                // Jika tidak, berarti file lokal storage
                $imageUrl = asset('storage/' . $this->image_url);
            }
        }
    return [
        'id' => $this->id,
        'title' => $this->title,
        'content' => $this->content,
        'image_url' => $imageUrl,
        'created_at' => $this->created_at?->diffForHumans(), // Contoh: "2 hours ago"
        'likes_count' => $this->likes_count, // Nanti di controller panggil withCount('likes')
        
        // Load relasi user jika tersedia
        'author' => new UserResource($this->whenLoaded('author')),
        
        // Load comments jika diminta
        'comments' => CommentResource::collection($this->whenLoaded('comments')),
    ];
}
}
