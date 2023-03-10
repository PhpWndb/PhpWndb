<?php

declare(strict_types=1);

namespace PhpWndb\Dataset\Parser;

use PhpWndb\Dataset\Model\Index\IndexEntry;
use PhpWndb\Dataset\Model\Index\IndexEntryFactory;
use PhpWndb\Dataset\Storage\Stream;

class IndexParser
{
    public function __construct(
        protected readonly TokenizerFactory $tokenizerFactory,
        protected readonly IndexEntryFactory $indexEntryFactory,
        protected readonly SyntacticCategoryMapper $syntacticCategoryMapper,
        protected readonly RelationPointerTypeMapper $relationPointerTypeMapper,
    ) {
    }

    public function parseIndex(Stream $stream): IndexEntry
    {
        $tokenizer = $this->tokenizerFactory->create($stream);

        $syntacticCategory = $this->syntacticCategoryMapper->mapSyntacticCategory($tokenizer->readString());

        $synsetsCount = $tokenizer->readDecimalInteger();
        $pointersCount = $tokenizer->readDecimalInteger();

        $pointerTypes = [];
        for ($i = 0; $i < $pointersCount; ++$i) {
            $pointerTypes[] = $this->relationPointerTypeMapper->mapRelationPointerType(
                $tokenizer->readString(),
                $syntacticCategory,
            );
        }

        // ignored sense count and tag sense count
        $tokenizer->readDecimalInteger();
        $tokenizer->readDecimalInteger();

        $synsetOffsets = [];
        for ($i = 0; $i < $synsetsCount; ++$i) {
            $synsetOffsets[] = $tokenizer->readDecimalInteger();
        }

        return $this->indexEntryFactory->create($syntacticCategory, $pointerTypes, $synsetOffsets);
    }
}
