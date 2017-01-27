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

    /**
     * @param bool|float|\Granam\Scalar\ScalarInterface|int|string $value
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidLevelRank
     * @throws \Doctrineum\Scalar\Exceptions\UnexpectedValueToEnum
     */
    public function __construct($value)
    {
        parent::__construct($value);
        if ($this->getValue() < 0) {
            throw new Exceptions\InvalidLevelRank(
                'Level can not be lesser than 0, got ' . ValueDescriber::describe($value)
            );
        }
    }

    /**
     * @return bool
     */
    public function isZeroLevel()
    {
        return $this->getValue() === 0;
    }

    /**
     * @return bool
     */
    public function isFirstLevel()
    {
        return $this->getValue() === 1;
    }

    /**
     * @return bool
     */
    public function isNextLevel()
    {
        return $this->getValue() > 1;
    }
}