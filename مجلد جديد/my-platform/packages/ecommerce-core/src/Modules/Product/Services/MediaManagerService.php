<?php

declare(strict_types=1);

namespace MyPlatform\EcommerceCore\Modules\Product\Services;

use MyPlatform\EcommerceCore\Modules\Product\Models\Product;
use MyPlatform\EcommerceCore\Modules\Product\Models\ProductMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaManagerService
{
    protected string $disk = 'public';
    protected string $basePath = 'products/media';

    /**
     * Upload multiple media files for a product
     *
     * @param array $files Array of UploadedFile instances
     * @param int $productId
     * @return array Created ProductMedia instances
     */
    public function uploadMultiple(array $files, int $productId): array
    {
        $uploadedMedia = [];
        $nextSortOrder = ProductMedia::where('product_id', $productId)->max('sort_order') ?? 0;

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $nextSortOrder++;
                $media = $this->uploadSingle($file, $productId, $nextSortOrder);
                $uploadedMedia[] = $media;
            }
        }

        return $uploadedMedia;
    }

    /**
     * Upload a single media file
     */
    public function uploadSingle(UploadedFile $file, int $productId, int $sortOrder = 0): ProductMedia
    {
        $type = $this->determineMediaType($file);
        
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = "{$this->basePath}/{$productId}";
        
        // Store file
        $storedPath = $file->storeAs($path, $filename, $this->disk);

        // Create media record
        return ProductMedia::create([
            'product_id' => $productId,
            'type' => $type,
            'path' => $storedPath,
            'url' => null,
            'is_featured' => false,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Add external video URL
     */
    public function addVideoUrl(string $url, int $productId, int $sortOrder = 0): ProductMedia
    {
        return ProductMedia::create([
            'product_id' => $productId,
            'type' => 'external_video',
            'path' => null,
            'url' => $url,
            'is_featured' => false,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Reorder product media
     */
    public function reorderMedia(int $productId, array $sortOrder): void
    {
        // $sortOrder is array where key is media_id and value is new sort_order
        foreach ($sortOrder as $mediaId => $order) {
            ProductMedia::where('id', $mediaId)
                ->where('product_id', $productId)
                ->update(['sort_order' => $order]);
        }
    }

    /**
     * Set featured image
     */
    public function setFeatured(int $mediaId, int $productId): void
    {
        // Remove featured from all other media
        ProductMedia::where('product_id', $productId)
            ->update(['is_featured' => false]);

        // Set this media as featured
        ProductMedia::where('id', $mediaId)
            ->where('product_id', $productId)
            ->update(['is_featured' => true]);
    }

    /**
     * Delete media and its file
     */
    public function deleteMedia(int $mediaId): bool
    {
        $media = ProductMedia::find($mediaId);
        
        if (!$media) {
            return false;
        }

        // Delete physical file if it exists
        if ($media->path && Storage::disk($this->disk)->exists($media->path)) {
            Storage::disk($this->disk)->delete($media->path);
        }

        return $media->delete();
    }

    /**
     * Get all media for a product
     */
    public function getProductMedia(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        return ProductMedia::where('product_id', $productId)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Determine media type from file
     */
    protected function determineMediaType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'image'; // Default
    }

    /**
     * Get media URL
     */
    public function getMediaUrl(ProductMedia $media): string
    {
        if ($media->url) {
            return $media->url; // External URL
        }

        if ($media->path) {
            return Storage::disk($this->disk)->url($media->path);
        }

        return '';
    }
}
