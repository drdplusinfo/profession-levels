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

class ProfessionFirstLevel extends ProfessionLevel
{

    /**
     * @param Profession $profession
     * @param \DateTimeImmutable|null $levelUpAt
     * @return ProfessionFirstLevel
     */
    public static function createFirstLevel(
        Profession $profession,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        return new static(
            $profession,
            new LevelRank(1),
            Strength::getIt(self::getBasePropertyFirstLevelModifier(Strength::STRENGTH, $profession)),
            Agility::getIt(self::getBasePropertyFirstLevelModifier(Agility::AGILITY, $profession)),
            Knack::getIt(self::getBasePropertyFirstLevelModifier(Knack::KNACK, $profession)),
            Will::getIt(self::getBasePropertyFirstLevelModifier(Will::WILL, $profession)),
            Intelligence::getIt(self::getBasePropertyFirstLevelModifier(Intelligence::INTELLIGENCE, $profession)),
            Charisma::getIt(self::getBasePropertyFirstLevelModifier(Charisma::CHARISMA, $profession)),
            $levelUpAt
        );
    }

    /**
     * @param string $propertyCode
     * @param Profession $profession
     *
     * @return int
     */
    private static function getBasePropertyFirstLevelModifier($propertyCode, Profession $profession)
    {
        return static::isProfessionPrimaryProperty($profession, $propertyCode)
            ? self::PRIMARY_PROPERTY_FIRST_LEVEL_MODIFIER
            : 0;
    }

    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() !== 1) {
            throw new Exceptions\InvalidFirstLevelRank(
                "First level has to have level rank 1, got {$levelRank->getValue()}"
            );
        }
    }

    /**
     * It is only the increment based on first level of specific profession.
     * There are other increments like race, size etc., solved in
     * @see \DrdPlus\Cave\UnitBundle\Person\Attributes\Properties\FirstLevelProperties
     *
     * @param BaseProperty $property
     * @param Profession $profession
     */
    protected function checkPropertyIncrement(BaseProperty $property, Profession $profession)
    {
        $propertyFirstLevelModifier = static::getBasePropertyFirstLevelModifier(
            $property->getCode(),
            $profession
        );
        if ($property->getValue() !== $propertyFirstLevelModifier) {
            throw new Exceptions\InvalidFirstLevelPropertyValue(
                "On first level has to be {$property->getCode()} of value {$propertyFirstLevelModifier}"
                . ", got {$property->getValue()}"
            );
        }
    }
}
