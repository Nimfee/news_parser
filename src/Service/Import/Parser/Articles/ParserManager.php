<?php

namespace App\Service\Import\Parser\Articles;


/**
 * Class File.
 */
class ParserManager
{
    /** @var ParserFactory  */
    protected $parserFactory;

    /**
     * ParserManager constructor.
     * @param ParserFactory $parserFactory
     */
    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    /**
     * Parses given resource and returns raw dat
     * @param string $uri
     * @param string $sourceType
     *
     * @return array
     */
    public function getData(string $uri, string $sourceType)
    {
        $parser = $this->parserFactory->create($sourceType);

        return $parser->parse($uri);
    }
}
