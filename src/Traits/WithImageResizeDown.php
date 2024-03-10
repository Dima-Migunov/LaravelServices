<?php

namespace Migunov\Services\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Migunov\Services\ImageConst;

trait WithImageResizeDown
{
    public static function resizeDown(
        string $path,
        int $width = 1200,
        int $height = 600,
        ?string $targetPath = null
    ): void {
        /** @var array */
        $result = self::initImage($path, $targetPath);

        if (!$result) {
            return;
        }

        /** @var \Intervention\Image\Interfaces\ImageInterface */
        $image = $result['image'];
        $target = $result['target'];

        if ($width > ImageConst::MAX_WIDTH || $width <= 0) {
            $width = ImageConst::MAX_WIDTH;
        }

        if ($height > ImageConst::MAX_HEIGHT || $height <= 0) {
            $height = ImageConst::MAX_HEIGHT;
        }

        $image->resizeDown($width, $height)->save($target, 90);
    }
}
