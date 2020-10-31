<?php

namespace App\Service\Import\Builder;

interface BuilderInterface
{
    public function createEntities(array $items): int
}
