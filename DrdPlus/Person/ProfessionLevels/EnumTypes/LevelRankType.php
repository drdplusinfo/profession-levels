<?php
namespace DrdPlus\Person\ProfessionLevels\EnumTypes;

use Doctrineum\Integer\IntegerEnumType;

class LevelRankType extends IntegerEnumType
{
    const LEVEL_RANK = 'level_rank';

    /**
     * @return string
     */
    public function getName()
    {
        return self::LEVEL_RANK;
    }
}
