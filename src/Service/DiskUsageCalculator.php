<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiskUsageCalculator
{
    public function __construct(
        private readonly string $publicPath
    ) {
    }

    public function getSpaceUsedByUsers(): float
    {
        $uploadFolderPath = $this->publicPath.'/uploads';

        if (is_dir($uploadFolderPath)) {
            return $this->getFolderSize($uploadFolderPath);
        }

        return 0;
    }

    public function getSpaceUsedByUser(User $user): float
    {
        $userFolderPath = $this->publicPath.'/uploads/'.$user->getId();

        if (is_dir($userFolderPath)) {
            return $this->getFolderSize($userFolderPath);
        }

        return 0;
    }

    public function hasEnoughSpaceForUpload(?User $user, File $file): bool
    {
        return !($user instanceof User && $file->isFile() && (float) $user->getDiskSpaceAllowed() - $this->getSpaceUsedByUser($user) < $file->getSize());
    }

    private function getFolderSize(string $path): float
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }
}
