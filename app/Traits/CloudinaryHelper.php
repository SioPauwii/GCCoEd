<?php

namespace App\Traits;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Illuminate\Support\Facades\Log;

trait CloudinaryHelper
{
    protected function generateCloudinaryUrl($publicId, $width = 400)
    {
        if (!$publicId) {
            return null;
        }

        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ]
            ]);
            
            return $cloudinary->image($publicId)
                ->resize(Resize::scale()->width($width))
                ->toUrl();
        } catch (\Exception $e) {
            Log::error('Failed to generate Cloudinary URL', [
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}