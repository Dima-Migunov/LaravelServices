<?php

namespace Migunov\Services\Traits;

use Migunov\Services\ImageConst;

trait WithImageResize
{
    public static function resize(
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

        $image = $result['image'];
        $target = $result['target'];

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

        if ($width > ImageConst::MAX_WIDTH) {
            $width = ImageConst::MAX_WIDTH;
        }

        if ($height > ImageConst::MAX_HEIGHT) {
            $height = ImageConst::MAX_HEIGHT;
        }

        if ($height == 0) {
            $image->scale(width: $width)->save($target, 90);
        } elseif ($width == 0) {
            $image->scale(height: $height)->save($target, 90);
        } elseif ($image->width() >= $image->height()) {
            $image->scale(width: $width)->save($target, 90);
        } else {
            $image->scale(height: $height)->save($target, 90);
        }
    }
}
