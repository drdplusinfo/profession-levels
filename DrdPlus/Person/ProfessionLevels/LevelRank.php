<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrineum\Integer\IntegerEnum;
use Granam\Tools\ValueDescriber;

/**
 * @method static LevelRank getEnum($value)
 */
class LevelRank extends IntegerEnum
{
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
        parent::__construct($value);
        if ($this->getValue() < 1) {
            throw new Exceptions\InvalidFirstLevelRank(
                'Level can not be lesser than 1, got ' . ValueDescriber::describe($value)
            );
        }
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
