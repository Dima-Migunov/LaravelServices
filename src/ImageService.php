<?php

namespace Migunov\Services;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Migunov\Services\Traits\WithImageFromUrlPage;
use Migunov\Services\Traits\WithImageResize;

class ImageService
{
    use WithImageFromUrlPage;
    use WithImageResize;

    public const IMAGE_EXTENSIONS = ['png', 'jpeg', 'jpg', 'svg', 'gif', 'webp', 'tiff'];

    public static function isImageFile(string $path): bool
    {
        $imageExts = implode('|', self::IMAGE_EXTENSIONS);
        return !!preg_match('#\.(' . $imageExts . ')$#', $path);
    }

    private static function initImage(string $path, string $targetPath): array
    {
        $abspath = Storage::disk('public')->path($path);
        $target = $targetPath ? Storage::disk('public')->path($targetPath) : $abspath;
        $image = ImageManager::gd();

        try {
            $image = $image->read($abspath);
        } catch (Exception) {
            return [];
        }

        return [
            'image' => $image,
            'target' => $target
        ];
    }
}
