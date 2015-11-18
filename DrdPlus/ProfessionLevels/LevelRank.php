<?php
namespace DrdPlus\ProfessionLevels;
use Doctrineum\Integer\IntegerEnum;

/**
 * @method static LevelRank getEnum($value)
 */
class LevelRank extends IntegerEnum
{
    const LEVEL_RANK = 'level_rank';

    /**
     * @param int $value
     *
     * @return LevelRank
     */
    public static function getIt($value)
    {
        return static::getEnum($value);
    }
}
