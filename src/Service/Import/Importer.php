<?php

namespace App\Service\Import;

use App\Service\Import\Builder\Articles\BuilderService;
use App\Service\Import\Parser\Articles\ParserManager;

class Importer
{
    /**
     * @var ParserManager $parserManager
     */
    protected $parserManager;

    /**
     * @var BuilderService $builderService
     */
    protected $builderService;

    /**
     * @param ParserManager $parserManager
     * @param BuilderService $builderService
     */
    public function __construct(ParserManager $parserManager, BuilderService $builderService)
    {
        $this->parserManager = $parserManager;
        $this->builderService = $builderService;
    }
    
    /**
     * @param string $uri
     * @param string $sourceType
     * @return int
     */
    public function processData(string $uri, string $sourceType): int
    {
        $data = $this->parserManager->getData($uri, $sourceType);

        if (count($data) === 0) {
            return null;
        }

        return $this->builderService->createEntities($data);
    }
}
