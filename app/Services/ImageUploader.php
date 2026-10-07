<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Enregistre les images envoyées depuis l'admin dans public/uploads/{dossier},
 * redimensionnées et converties en WebP (poids divisé par 3 à 10).
 */
class ImageUploader
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    /** @return string chemin relatif à public/ (ex. uploads/news/abc.webp) */
    public function store(UploadedFile $file, string $directory, int $maxWidth = 1600): string
    {
        $directory = trim($directory, '/');
        $dir = public_path("uploads/$directory");
        File::ensureDirectoryExists($dir);

        $name = Str::uuid().'.webp';
        $this->manager->read($file->getRealPath())
            ->scaleDown(width: $maxWidth)
            ->toWebp(82)
            ->save("$dir/$name");

        return "uploads/$directory/$name";
    }

    /** Remplace une image : enregistre la nouvelle puis supprime l'ancienne. */
    public function replace(?UploadedFile $file, ?string $old, string $directory, int $maxWidth = 1600): ?string
    {
        if (! $file) {
            return $old;
        }
        $path = $this->store($file, $directory, $maxWidth);
        $this->delete($old);

        return $path;
    }

    /** Supprime un fichier uniquement s'il se trouve dans public/uploads. */
    public function delete(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
            return;
        }
        File::delete(public_path($path));
    }
}
