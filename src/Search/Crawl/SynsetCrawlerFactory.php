<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Search\Crawl;

use PhpWndb\Dataset\Model\Data\Synset;
use PhpWndb\Dataset\Model\SynsetId\SynsetIdFactory;
use PhpWndb\Dataset\Search\Data\SynsetSearchEngine;

class SynsetCrawlerFactory
{
    public function __construct(
        protected readonly WordCrawlerFactory $wordCrawlerFactory,
        protected readonly SynsetSearchEngine $synsetSearchEngine,
        protected readonly SynsetIdFactory $synsetIdFactory,
    ) {
    }

    /**
     * @param Synset[] $synsets
     * @return SynsetCrawler[]
     */
    public function createFromSynsets(array $synsets, SynsetListCrawlerFactory $listCrawlerFactory): array
    {
        return \array_map(
            fn (Synset $synset) => $this->createFromSynset($synset, $listCrawlerFactory),
            $synsets,
        );
    }

    protected function createFromSynset(Synset $synset, SynsetListCrawlerFactory $listCrawlerFactory): SynsetCrawler
    {
        return new SynsetCrawler(
            synset: $synset,
            synsetIdFactory: $this->synsetIdFactory,
            wordCrawlerFactory: $this->wordCrawlerFactory,
            synsetListCrawlerFactory: $listCrawlerFactory,
            synsetSearchEngine: $this->synsetSearchEngine,
        );
    }
}
