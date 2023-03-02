<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Search\Crawl;

use LogicException;
use PhpWndb\Dataset\Model\Data\Synset;
use PhpWndb\Dataset\Model\Data\Word;
use PhpWndb\Dataset\Model\RelationPointerType;
use PhpWndb\Dataset\Search\Data\SynsetSearchEngine;

class WordCrawler
{
    public function __construct(
        protected readonly int $wordIndex,
        protected readonly Synset $synset,
        protected readonly SynsetSearchEngine $synsetSearchEngine,
        protected readonly WordCrawlerFactory $wordCrawlerFactory,
        protected readonly WordListCrawlerFactory $wordListCrawlerFactory,
    ) {
    }

    public function moveTo(RelationPointerType $pointerType): WordListCrawler
    {
        $wordCrawlers = [];
        foreach ($this->synset->getRelationPointers() as $pointer) {
            if (
                $pointer->getSourceSynsetWordIndex() !== $this->wordIndex
                || $pointer->getTargetSynsetWordIndex() === null
                || $pointer->getType() !== $pointerType
            ) {
                continue;
            }

            $synset = $this->synsetSearchEngine->getByRelation($pointer);

            $wordCrawlers[] = $this->wordCrawlerFactory->create($pointer->getTargetSynsetWordIndex(), $synset);
        }

        return $this->wordListCrawlerFactory->create($wordCrawlers);
    }

    public function toString(): string
    {
        return $this->getWord()->getValue();
    }

    protected function getWord(): Word
    {
        return $this->synset->getWords()[$this->wordIndex]
            ?? throw new LogicException(
                "Synset `{$this->synset->getOffset()}` doesn't contains word with index `{$this->wordIndex}`.",
            );
    }
}
