<?php

namespace Migunov\Services;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{
    public const MAX_WIDTH = 1200;
    public const MAX_HEIGHT = 600;

    public const PREVIEW_MAX_WIDTH = 600;
    public const PREVIEW_MAX_HEIGHT = 400;

    public const MINI_MAX_WIDTH = 64;
    public const MINI_MAX_HEIGHT = 64;

    public static function resize(
        string $path,
        int $width = 1200,
        int $height = 600,
        ?string $targetPath = null
    ): void {
        $abspath = Storage::disk('public')->path($path);
        $target = $targetPath ? Storage::disk('public')->path($targetPath) : $abspath;
        $image = ImageManager::gd();

        try {
            $image = $image->read($abspath);
        } catch(Exception) {
            return;
        }

        if ($width <= 0) {
            $width = $image->width();
        }

        if ($width > self::MAX_WIDTH) {
            $width = self::MAX_WIDTH;
        }

        if ($height <= 0) {
            $height = $image->height();
        }

        if ($height > self::MAX_HEIGHT) {
            $height = self::MAX_HEIGHT;
        }

        if ($image->width() >= $image->height()) {
            $image->scale(width:$width)->save($target, 90);
        } else {
            $image->scale(height:$height)->save($target, 90);
        }
    }
}
