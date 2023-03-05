<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Tests;

use PHPUnit\Framework\TestCase;
use PhpWndb\Dataset\Model\Data\SynsetType;
use PhpWndb\Dataset\Model\Index\SyntacticCategory;
use PhpWndb\Dataset\Model\RelationPointerType;
use PhpWndb\Dataset\WordNetProvider;

class IntegrationTest extends TestCase
{
    public function test(): void
    {
        $wordNet = (new WordNetProvider())->getWordNet();
        $synsets = $wordNet->search('happy');

        self::assertCount(4, $synsets);

        $adjSynsets = $synsets->onlyType(SynsetType::ADJECTIVE);

        self::assertCount(1, $adjSynsets);

        $synset = $adjSynsets->getFirst();

        self::assertSame(
            'enjoying or showing or marked by joy or pleasure; "a happy smile"; "spent many happy days on the beach"; "a happy marriage"',
            $synset->getGloss(),
        );
        self::assertSame(SynsetType::ADJECTIVE, $synset->getType());
        self::assertSame(SyntacticCategory::ADJECTIVE, $synset->getSynsetId()->getSyntacticCategory());
        self::assertSame(1151786, $synset->getSynsetId()->getSynsetOffset());
        self::assertSame('aj1151786', $synset->getSynsetId()->toString());
        self::assertCount(1, $synset);

        $word = $synset->getFirst();
        self::assertSame('happy', $word->toString());

        $antonyms = $word->moveTo(RelationPointerType::ANTONYM);
        self::assertCount(1, $antonyms);

        $antonym = $antonyms->getLast();
        self::assertSame('unhappy', $antonym->toString());

        self::assertCount(0, $adjSynsets->omitType(SynsetType::ADJECTIVE));
    }
}
