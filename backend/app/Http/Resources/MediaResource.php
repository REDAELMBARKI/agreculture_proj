<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [];
        }

        return [
            'id' => $this->id,
            'path' => $this->path,
            'file_path' => $this->path,
            'url' => $this->resolveUrl(),
            'collection' => $this->collection,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'sort_order' => $this->sort_order,
        ];
    }

    protected function resolveUrl(): string
    {
        $disk = $this->disk ?: 'public';
        $path = $this->path;
        $rawUrl = (string) ($this->url ?? '');

        if (! empty($path) && Storage::disk($disk)->exists($path)) {
            return url('/api/media/file/' . $this->id);
        }

        if (! empty($rawUrl)) {
            return $rawUrl;
        }

        return url('/api/media/file/' . $this->id);
    }
}
