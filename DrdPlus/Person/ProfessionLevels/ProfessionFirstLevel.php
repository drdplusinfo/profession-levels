<?php
declare(strict_types = 1);

namespace DrdPlus\Person\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;

/**
 * @ORM\Entity()
 */
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
    ): ProfessionFirstLevel
    {
        return new static(
            $profession,
            new LevelRank(1),
            Strength::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::STRENGTH), $profession)),
            Agility::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::AGILITY), $profession)),
            Knack::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::KNACK), $profession)),
            Will::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::WILL), $profession)),
            Intelligence::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::INTELLIGENCE), $profession)),
            Charisma::getIt(self::getBasePropertyFirstLevelModifier(PropertyCode::getIt(PropertyCode::CHARISMA), $profession)),
            $levelUpAt
        );
    }

    public const PRIMARY_PROPERTY_FIRST_LEVEL_MODIFIER = 1;

    /**
     * @param PropertyCode $propertyCode
     * @param Profession $profession
     * @return int
     */
    private static function getBasePropertyFirstLevelModifier(PropertyCode $propertyCode, Profession $profession): int
    {
        return static::isProfessionPrimaryProperty($profession, $propertyCode)
            ? self::PRIMARY_PROPERTY_FIRST_LEVEL_MODIFIER
            : 0;
    }

    /**
     * @param LevelRank $levelRank
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelRank
     */
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
     * There are other increments like race, size etc., solved in different library.
     *
     * @param BaseProperty $baseProperty
     * @param Profession $profession
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidFirstLevelPropertyValue
     */
    protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession)
    {
        $propertyFirstLevelModifier = static::getBasePropertyFirstLevelModifier(
            PropertyCode::getIt($baseProperty->getCode()),
            $profession
        );
        if ($baseProperty->getValue() !== $propertyFirstLevelModifier) {
            throw new Exceptions\InvalidFirstLevelPropertyValue(
                "On first level has to be {$baseProperty->getCode()} of value {$propertyFirstLevelModifier}"
                . ", got {$baseProperty->getValue()}"
            );
        }
    }
}