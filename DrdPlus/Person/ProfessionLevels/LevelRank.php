<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrineum\Integer\IntegerEnum;
use Granam\Scalar\Tools\ValueDescriber;

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

    public function __construct($value)
    {
        if ($value < 1) {
            throw new Exceptions\MinimumLevelExceeded(
                'Level can not be lesser than 1, got ' . ValueDescriber::describe($value)
            );
        }
        parent::__construct($value);
    }

    /**
     * @return bool
     */
    public function isFirstLevel()
    {
        return $this->getValue() === 1;
    }

    public function isNextLevel()
    {
        return $this->getValue() > 1;
    }
}
