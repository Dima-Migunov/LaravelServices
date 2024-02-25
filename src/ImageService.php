<?php

namespace Migunov\Services;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Migunov\Services\Traits\WithImageFromUrlPage;

class ImageService
{
    use WithImageFromUrlPage;

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

        if ($width == 0 && $height == 0) {
            $width = $image->width();
            $height = $image->height();
        }

        if ($width < 0) {
            $width = $image->width();
        }

        if ($height < 0) {
            $height = $image->height();
        }

        if ($width > self::MAX_WIDTH) {
            $width = self::MAX_WIDTH;
        }

        if ($height > self::MAX_HEIGHT) {
            $height = self::MAX_HEIGHT;
        }

        if ($height == 0) {
            $image->scale(width:$width)->save($target, 90);
        } elseif ($width == 0) {
            $image->scale(height:$height)->save($target, 90);
        } elseif ($image->width() >= $image->height()) {
            $image->scale(width:$width)->save($target, 90);
        } else {
            $image->scale(height:$height)->save($target, 90);
        }
    }
}
