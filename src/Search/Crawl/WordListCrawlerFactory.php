<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Search\Crawl;

class WordListCrawlerFactory
{
    /**
     * @param WordCrawler[] $wordCrawlers
     */
    public function create(array $wordCrawlers): WordListCrawler
    {
        return new WordListCrawler($wordCrawlers);
    }
}
