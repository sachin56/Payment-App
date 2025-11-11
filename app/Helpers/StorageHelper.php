<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StorageHelper
{

    private $file;
    private $imageName;
    private $folder;

    private $disk;

    public function __construct($folder, $imageName, $file = null)
    {
        $this->folder = $folder;
        $this->imageName = $imageName;
        $this->file = $file;
        $this->disk = 'public';
    }

    /**
     * Uploads an image to the specified disk and returns the URL of the uploaded image.
     *
     * @return string|void The URL of the uploaded image, or void if the upload fails.
     */
    public function uploadImage()
    {
        try {
            $imagePath = $this->folder . '/' . $this->imageName;
            Storage::disk($this->disk)->put($imagePath, file_get_contents($this->file));
            return Storage::disk($this->disk)->url($imagePath);
        } catch (\Exception $e) {
            Log::error('Image Upload Failed', [$e->getMessage()]);
        }
    }

    /**
     * Deletes the specified image from the storage disk.
     *
     * @return void
     */
    public function deleteImage()
    {
        try {
            $path = $this->folder . '/' . $this->imageName;
            Storage::disk($this->disk)->delete($path);
        } catch (\Exception $e) {
            Log::error('Image Delete Failed', [$e->getMessage()]);
        }
    }

    /**
     * Retrieves the URL of the image.
     *
     * @return string The URL of the image.
     */
    public function getUrl(): string
    {
        $imagePath = $this->folder . '/' . $this->imageName;
        return Storage::disk($this->disk)->url($imagePath);
    }
}
