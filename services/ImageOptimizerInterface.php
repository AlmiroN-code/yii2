<?php

namespace app\services;

/**
 * ImageOptimizerInterface - интерфейс оптимизатора изображений.
 * Requirements: 4.1-4.5
 */
interface ImageOptimizerInterface
{
    /**
     * Optimizes image (resize, convert to WebP).
     * 
     * @param string $sourcePath Path to source image
     * @param array $options Optimization options
     * @return string Path to optimized image
     */
    public function optimize(string $sourcePath, array $options = []): string;

    /**
     * Creates thumbnail versions of image.
     * 
     * @param string $sourcePath Path to source image
     * @return array Paths to created thumbnails [size => path]
     */
    public function createThumbnails(string $sourcePath): array;

    /**
     * Converts image to WebP format.
     * 
     * @param string $sourcePath Path to source image
     * @param int $quality Quality (0-100)
     * @return string Path to WebP image
     */
    public function convertToWebp(string $sourcePath, int $quality = 85): string;

    /**
     * Deletes image and all its thumbnails.
     * 
     * @param string $path Path to image
     * @return bool Success
     */
    public function delete(string $path): bool;
}
