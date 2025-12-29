<?php

namespace app\services;

use Yii;

/**
 * ImageOptimizer - сервис оптимизации изображений.
 * Requirements: 4.1-4.5
 */
class ImageOptimizer implements ImageOptimizerInterface
{
    const SIZE_SMALL = [150, 150];
    const SIZE_MEDIUM = [400, 300];
    const SIZE_LARGE = [800, 600];
    const MAX_WIDTH = 1920;

    private $sizes = [
        'small' => self::SIZE_SMALL,
        'medium' => self::SIZE_MEDIUM,
        'large' => self::SIZE_LARGE,
    ];

    /**
     * {@inheritdoc}
     */
    public function optimize(string $sourcePath, array $options = []): string
    {
        if (!$this->isGdAvailable()) {
            Yii::warning('GD library not available, skipping optimization', __METHOD__);
            return $sourcePath;
        }

        $maxWidth = $options['maxWidth'] ?? self::MAX_WIDTH;
        $quality = $options['quality'] ?? 85;

        $image = $this->loadImage($sourcePath);
        if (!$image) {
            return $sourcePath;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Resize if too large
        if ($width > $maxWidth) {
            $newHeight = (int)($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($maxWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Convert to WebP
        $webpPath = $this->changeExtension($sourcePath, 'webp');
        imagewebp($image, $webpPath, $quality);
        imagedestroy($image);

        // Delete original if different
        if ($sourcePath !== $webpPath && file_exists($sourcePath)) {
            unlink($sourcePath);
        }

        return $webpPath;
    }

    /**
     * {@inheritdoc}
     */
    public function createThumbnails(string $sourcePath): array
    {
        $thumbnails = [];

        if (!$this->isGdAvailable()) {
            Yii::warning('GD library not available, skipping thumbnails', __METHOD__);
            return $thumbnails;
        }

        $image = $this->loadImage($sourcePath);
        if (!$image) {
            return $thumbnails;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $dir = dirname($sourcePath);
        $filename = pathinfo($sourcePath, PATHINFO_FILENAME);

        foreach ($this->sizes as $sizeName => [$targetWidth, $targetHeight]) {
            $thumb = $this->createThumbnail($image, $width, $height, $targetWidth, $targetHeight);
            $thumbPath = $dir . '/' . $filename . '_' . $sizeName . '.webp';
            imagewebp($thumb, $thumbPath, 85);
            imagedestroy($thumb);
            $thumbnails[$sizeName] = $thumbPath;
        }

        imagedestroy($image);
        return $thumbnails;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToWebp(string $sourcePath, int $quality = 85): string
    {
        if (!$this->isGdAvailable()) {
            Yii::warning('GD library not available, skipping WebP conversion', __METHOD__);
            return $sourcePath;
        }

        $image = $this->loadImage($sourcePath);
        if (!$image) {
            return $sourcePath;
        }

        $webpPath = $this->changeExtension($sourcePath, 'webp');
        imagewebp($image, $webpPath, $quality);
        imagedestroy($image);

        return $webpPath;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $path): bool
    {
        $deleted = false;
        $dir = dirname($path);
        $filename = pathinfo($path, PATHINFO_FILENAME);

        // Delete main file
        if (file_exists($path)) {
            unlink($path);
            $deleted = true;
        }

        // Delete thumbnails
        foreach (array_keys($this->sizes) as $sizeName) {
            $thumbPath = $dir . '/' . $filename . '_' . $sizeName . '.webp';
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }

        return $deleted;
    }

    /**
     * Checks if GD library is available.
     */
    private function isGdAvailable(): bool
    {
        return extension_loaded('gd') && function_exists('imagecreatetruecolor');
    }

    /**
     * Loads image from file.
     */
    private function loadImage(string $path)
    {
        if (!file_exists($path)) {
            return null;
        }

        $info = getimagesize($path);
        if (!$info) {
            return null;
        }

        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($path);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                return $image;
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            default:
                return null;
        }
    }

    /**
     * Creates thumbnail with crop to fit.
     */
    private function createThumbnail($image, int $srcWidth, int $srcHeight, int $dstWidth, int $dstHeight)
    {
        $srcRatio = $srcWidth / $srcHeight;
        $dstRatio = $dstWidth / $dstHeight;

        if ($srcRatio > $dstRatio) {
            $cropWidth = (int)($srcHeight * $dstRatio);
            $cropHeight = $srcHeight;
            $cropX = (int)(($srcWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $srcWidth;
            $cropHeight = (int)($srcWidth / $dstRatio);
            $cropX = 0;
            $cropY = (int)(($srcHeight - $cropHeight) / 2);
        }

        $thumb = imagecreatetruecolor($dstWidth, $dstHeight);
        imagecopyresampled($thumb, $image, 0, 0, $cropX, $cropY, $dstWidth, $dstHeight, $cropWidth, $cropHeight);

        return $thumb;
    }

    /**
     * Changes file extension.
     */
    private function changeExtension(string $path, string $newExt): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . '/' . $info['filename'] . '.' . $newExt;
    }
}
