<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Search\Crawl;

use PhpWndb\Dataset\Model\Data\Synset;
use PhpWndb\Dataset\Search\Data\SynsetSearchEngine;

class WordCrawlerFactory
{
    public function __construct(
        protected readonly SynsetSearchEngine $synsetSearchEngine,
        protected readonly WordListCrawlerFactory $wordListCrawlerFactory,
    ) {
    }

    /**
     * @return WordCrawler[]
     */
    public function createAllFromSynset(Synset $synset): array
    {
        $words = [];
        foreach ($synset->getWords() as $i => $word) {
            $words[] = $this->create($i, $synset);
        }

        return $words;
    }

    public function create(int $wordIndex, Synset $synset): WordCrawler
    {
        return new WordCrawler(
            wordIndex: $wordIndex,
            synset: $synset,
            synsetSearchEngine: $this->synsetSearchEngine,
            wordCrawlerFactory: $this,
            wordListCrawlerFactory: $this->wordListCrawlerFactory,
        );
    }
}
