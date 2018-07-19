<?php
declare(strict_types = 1);

namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use Doctrineum\Integer\IntegerEnumType;

class LevelRankType extends IntegerEnumType
{
    public const LEVEL_RANK = 'level_rank';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::LEVEL_RANK;
    }
}