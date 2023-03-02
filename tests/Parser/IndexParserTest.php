<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Tests\Parser;

use Iterator;
use PHPUnit\Framework\TestCase;
use PhpWndb\Dataset\Model\Index\IndexEntryFactory;
use PhpWndb\Dataset\Model\Index\SyntacticCategory;
use PhpWndb\Dataset\Model\RelationPointerType;
use PhpWndb\Dataset\Parser\IndexParser;
use PhpWndb\Dataset\Parser\RelationPointerTypeMapper;
use PhpWndb\Dataset\Parser\SyntacticCategoryMapper;
use PhpWndb\Dataset\Parser\TokenizerFactory;
use PhpWndb\Dataset\Storage\StringStream;

class IndexParserTest extends TestCase
{
    /**
     * @param RelationPointerType[] $pointerTypes
     * @param int[] $synsetOffsets
     * @dataProvider dpTestParse
     */
    public function testParse(
        string $data,
        SyntacticCategory $syntacticCategory,
        array $pointerTypes,
        array $synsetOffsets,
    ): void {
        $parser = new IndexParser(
            tokenizerFactory: new TokenizerFactory(),
            indexEntryFactory: new IndexEntryFactory(),
            syntacticCategoryMapper: new SyntacticCategoryMapper(),
            relationPointerTypeMapper: new RelationPointerTypeMapper(),
        );
        $index = $parser->parseIndex(new StringStream($data));

        self::assertSame($syntacticCategory, $index->getSyntacticCategory());
        self::assertSame($pointerTypes, $index->getRelationPointerTypes());
        self::assertSame($synsetOffsets, $index->getSynsetOffsets());
    }

    /**
     * @return Iterator<mixed>
     */
    public static function dpTestParse(): Iterator
    {
        // Noun
        yield [
            'n 3 3 @ ~ + 3 0 01206166 01055844 01023831',
            SyntacticCategory::NOUN,
            [
                RelationPointerType::HYPERNYM,
                RelationPointerType::HYPONYM,
                RelationPointerType::DERIVATIONALLY_RELATED_FORM,
            ],
            [1206166, 1055844, 1023831],
        ];
        // Verb
        yield [
            'v 2 3 @ > ; 2 0 02519545 01801593',
            SyntacticCategory::VERB,
            [RelationPointerType::HYPERNYM, RelationPointerType::CAUSE, RelationPointerType::DOMAIN_OF_SYNSET],
            [2519545, 1801593],
        ];
    }
}
