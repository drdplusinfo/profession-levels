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
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
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
    const MAXIMUM_NEXT_LEVEL = 20;

    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() < self::MINIMUM_NEXT_LEVEL) {
            throw new Exceptions\MinimumLevelExceeded(
                "Next level can not be lesser than " . self::MINIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
        if ($levelRank->getValue() > self::MAXIMUM_NEXT_LEVEL) {
            throw new Exceptions\MaximumLevelExceeded(
                "Level can not be greater than " . self::MAXIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
    }

    const MAX_NEXT_LEVEL_PROPERTY_MODIFIER = 1;

    protected function checkPropertyIncrement(BaseProperty $property, Profession $profession)
    {
        if ($property->getValue() < 0) {
            throw new Exceptions\NegativeNextLevelProperty(
                "Next level property increment can not be negative, got {$property->getValue()}"
            );
        }
        if ($property->getValue() > self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER) {
            throw new Exceptions\TooHighNextLevelPropertyIncrement(
                'Next level property increment has to be at most '
                . self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER . ", got {$property->getValue()}"
            );
        }
    }
}