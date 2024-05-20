<?php

namespace App\Service;

interface BinListServiceInterface
{
    public function getBinData(string $bin): array;
    public function isEu(string $countryCode): bool;
}
