<?php
namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;

class ProfessionNextLevel extends ProfessionLevel
{

    /**
     * @param Profession $profession
     * @param LevelRank $nextLevelRank
     * @param Strength $strengthIncrement
     * @param Agility $agilityIncrement
     * @param Knack $knackIncrement
     * @param Will $willIncrement
     * @param Intelligence $intelligenceIncrement
     * @param Charisma $charismaIncrement
     * @param \DateTimeImmutable|null $levelUpAt
     * @return ProfessionNextLevel
     */
    public static function createNextLevel(
        Profession $profession,
        LevelRank $nextLevelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        return new static(
            $profession, $nextLevelRank, $strengthIncrement, $agilityIncrement, $knackIncrement,
            $willIncrement, $intelligenceIncrement, $charismaIncrement, $levelUpAt
        );
    }

    const MINIMUM_NEXT_LEVEL = 2;

    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() > self::MAXIMUM_LEVEL) {
            throw new Exceptions\MaximumLevelExceeded(
                "Level can not be greater than " . self::MAXIMUM_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
        if ($levelRank->getValue() < self::MINIMUM_NEXT_LEVEL) {
            throw new Exceptions\MinimumLevelExceeded(
                "Next level can not be lesser than " . self::MINIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
    }

    protected function checkPropertyIncrement(BaseProperty $property, Profession $profession)
    {
        if ($property->getValue() < self::MIN_NEXT_LEVEL_PROPERTY_MODIFIER // 0
            || $property->getValue() > self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER // 1
        ) {
            throw new Exceptions\InvalidNextLevelPropertyValue(
                'Next level property change has to be between '
                . self::MIN_NEXT_LEVEL_PROPERTY_MODIFIER . ' and '
                . self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER . ", got {$property->getValue()}"
            );
        }
    }
}