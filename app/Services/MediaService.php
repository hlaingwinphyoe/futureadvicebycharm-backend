<?php

namespace App\Services;

class MediaService
{
    public function storeMedia(array $formData = [])
    {
        $file = $formData['media'];
        $url = $this->saveImage($file, $formData['type']);

        return $url;
    }

    protected function saveImage($file, $type)
    {
        // Get file extension
        $fileNameWithExt = $file->getClientOriginalExtension();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $fileNameToStore = $fileName . '_' . time() . '.' . $file->extension();

        // Store the file in the 'public/images' folder
        $filePath = $file->storeAs($type, $fileNameToStore, 'public');

        return $filePath;
    }
}
