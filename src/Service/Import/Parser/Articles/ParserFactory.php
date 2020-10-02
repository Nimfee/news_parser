<?php

namespace App\Service\Import\Parser\Articles;

class ParserFactory
{
    const PARSER_RBK = 'rbk';

    /**
     * @var ParserRbk $parserBpi
     */
    protected $parserRbk;

    public function __construct(iterable $parsers)
    {
        $handlers = iterator_to_array($parsers);
        $this->parserRbk = $handlers['rbk'];
    }

    /**
     * @param string $sourceType
     * @return ParserRbk|array
     */
    public function create(string $sourceType)
    {
        switch (strtolower($sourceType)) {
            case self::PARSER_RBK:
                return $this->parserRbk;
            default:
                throw new \InvalidArgumentException('Missing reader type');
        }
    }
}
