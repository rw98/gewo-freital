<?php

namespace App\Immonet\DTO;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class Attachment
{
    public function __construct(
        public string $content,
        public string $filename,
        public string $mimeType,
        public string $title = '',
        public bool $isFloorplan = false,
        public bool $isTitlePicture = false,
    ) {}

    /**
     * Create an Attachment DTO from an Image model.
     */
    public static function fromImage(Image $image, bool $isTitlePicture = false): self
    {
        $content = Storage::get($image->path);

        if ($content === null) {
            throw new \RuntimeException("Could not read image file: {$image->path}");
        }

        return new self(
            content: $content,
            filename: $image->filename ?? basename($image->path),
            mimeType: $image->mime_type ?? 'image/jpeg',
            title: $image->alt ?? '',
            isFloorplan: false,
            isTitlePicture: $isTitlePicture,
        );
    }

    /**
     * Create an Attachment DTO from a file path.
     */
    public static function fromPath(string $path, string $title = '', bool $isTitlePicture = false): self
    {
        $content = Storage::get($path);

        if ($content === null) {
            throw new \RuntimeException("Could not read file: {$path}");
        }

        $mimeType = Storage::mimeType($path) ?? 'application/octet-stream';

        return new self(
            content: $content,
            filename: basename($path),
            mimeType: $mimeType,
            title: $title,
            isTitlePicture: $isTitlePicture,
        );
    }
}
