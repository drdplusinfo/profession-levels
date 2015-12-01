<?php
namespace DrdPlus\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Profession;
use Granam\Scalar\Tools\ValueDescriber;
use Granam\Strict\Object\StrictObject;

/**
 * TODO what about classes ProfessionFirstLevel and ProfessionNextLevel ?
 */
abstract class ProfessionLevel extends StrictObject
{

    const PRIMARY_PROPERTY_FIRST_LEVEL_MODIFIER = 1;
    const MINIMUM_LEVEL = 1;
    const MAXIMUM_LEVEL = 20;
    const MIN_NEXT_LEVEL_PROPERTY_MODIFIER = 0;
    const MAX_NEXT_LEVEL_PROPERTY_MODIFIER = 1;
    const PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;
    const SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;

    /**
     * Have to be protected to allow Doctrine to access it on children
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Profession
     *
     * @ORM\Column(type="profession")
     */
    private $profession;

    /**
     * @var LevelRank
     *
     * @ORM\Column(type="levelValue")
     */
    private $levelRank;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetimeImmutable")
     */
    private $levelUpAt;

    /**
     * @var Strength
     *
     * @ORM\Column(type="strength")
     */
    private $strengthIncrement;

    /**
     * @var Agility
     *
     * @ORM\Column(type="agility")
     */
    private $agilityIncrement;

    /**
     * @var Knack
     *
     * @ORM\Columns(type="knack")
     */
    private $knackIncrement;

    /**
     * @var Will
     *
     * @ORM\Column(type="will")
     */
    private $willIncrement;

    /**
     * @var Intelligence
     *
     * @ORM\Column(type="intelligence")
     */
    private $intelligenceIncrement;

    /**
     * @var Charisma
     *
     * @ORM\Column(type="charisma")
     */
    private $charismaIncrement;

    protected static function createFirstLevelFor(
        Profession $profession,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        return new static(
            $profession,
            new LevelRank(1),
            Strength::getIt(static::getBasePropertyFirstLevelModifier(Strength::STRENGTH, $profession)),
            Agility::getIt(static::getBasePropertyFirstLevelModifier(Agility::AGILITY, $profession)),
            Knack::getIt(static::getBasePropertyFirstLevelModifier(Knack::KNACK, $profession)),
            Will::getIt(static::getBasePropertyFirstLevelModifier(Will::WILL, $profession)),
            Intelligence::getIt(static::getBasePropertyFirstLevelModifier(Intelligence::INTELLIGENCE, $profession)),
            Charisma::getIt(static::getBasePropertyFirstLevelModifier(Charisma::CHARISMA, $profession)),
            $levelUpAt
        );
    }

    protected function __construct(
        Profession $profession,
        LevelRank $levelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        $this->checkLevelRank($levelRank);
        $this->checkPropertyIncrement($strengthIncrement, $levelRank, $profession);
        $this->checkPropertyIncrement($agilityIncrement, $levelRank, $profession);
        $this->checkPropertyIncrement($knackIncrement, $levelRank, $profession);
        $this->checkPropertyIncrement($willIncrement, $levelRank, $profession);
        $this->checkPropertyIncrement($intelligenceIncrement, $levelRank, $profession);
        $this->checkPropertyIncrement($charismaIncrement, $levelRank, $profession);
        $this->checkPropertySumIncrement(
            $levelRank,
            $strengthIncrement,
            $agilityIncrement,
            $knackIncrement,
            $willIncrement,
            $intelligenceIncrement,
            $charismaIncrement
        );

        $this->profession = $profession;
        $this->levelRank = $levelRank;
        $this->strengthIncrement = $strengthIncrement;
        $this->agilityIncrement = $agilityIncrement;
        $this->knackIncrement = $knackIncrement;
        $this->willIncrement = $willIncrement;
        $this->intelligenceIncrement = $intelligenceIncrement;
        $this->charismaIncrement = $charismaIncrement;
        $this->levelUpAt = $levelUpAt ?: new \DateTimeImmutable();
    }

    private function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() > self::MAXIMUM_LEVEL) {
            throw new Exceptions\MaximumLevelExceeded(
                "Level can not be greater than " . self::MAXIMUM_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
        if ($levelRank->getValue() < self::MINIMUM_LEVEL) {
            throw new Exceptions\MinimumLevelExceeded(
                "Level can not be lesser than " . self::MINIMUM_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
    }

    private function checkPropertySumIncrement(
        LevelRank $levelRank,
        Strength $strength,
        Agility $agility,
        Knack $knack,
        Will $will,
        Intelligence $intelligence,
        Charisma $charisma
    )
    {
        $sumOfProperties = $this->sumProperties($strength, $agility, $knack, $will, $intelligence, $charisma);
        if ($levelRank->isNextLevel()) { // note: first level properties are covered by one-by-one tests
            if ($sumOfProperties !== $this->getExpectedSumOfNextLevelProperties()) {
                throw new Exceptions\InvalidNextLevelPropertiesSum(
                    "Sum of {$levelRank->getValue()}. level properties should be "
                    . $this->getExpectedSumOfNextLevelProperties()
                    . ', got ' . $sumOfProperties
                );
            }
        }
    }

    private function getExpectedSumOfNextLevelProperties()
    {
        return static::PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM + static::SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM;
    }

    private function sumProperties(
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement
    )
    {
        return $strengthIncrement->getValue() + $agilityIncrement->getValue() + $knackIncrement->getValue()
        + $willIncrement->getValue() + $intelligenceIncrement->getValue() + $charismaIncrement->getValue();
    }

    private function checkPropertyIncrement(BaseProperty $property, LevelRank $levelRank, Profession $profession)
    {
        if ($levelRank->isFirstLevel()) {
            $this->checkPropertyFirstLevelIncrement($property, $profession);
        } else {
            $this->checkNextLevelPropertyIncrement($property);
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
    private function checkPropertyFirstLevelIncrement(BaseProperty $property, Profession $profession)
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

    private function checkNextLevelPropertyIncrement(BaseProperty $property)
    {
        if ($property->getValue() < self::MIN_NEXT_LEVEL_PROPERTY_MODIFIER
            || $property->getValue() > self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER
        ) {
            throw new \LogicException(
                'Next level property change has to be between '
                . self::MIN_NEXT_LEVEL_PROPERTY_MODIFIER . ' and '
                . self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER . ", got {$property->getValue()}"
            );
        }
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLevelUpAt()
    {
        return $this->levelUpAt;
    }

    /**
     * @return LevelRank
     */
    public function getLevelRank()
    {
        return $this->levelRank;
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

    /** @return bool */
    public function isFirstLevel()
    {
        return $this->getLevelRank()->getValue() === 1;
    }

    public function isNextLevel()
    {
        return $this->getLevelRank()->getValue() > 1;
    }

    /**
     * @param string $propertyCode
     *
     * @return bool
     */
    public function isPrimaryProperty($propertyCode)
    {
        return static::isProfessionPrimaryProperty($this->getProfession(), $propertyCode);
    }

    private static function isProfessionPrimaryProperty(Profession $profession, $propertyCode)
    {
        return $profession->isPrimaryProperty($propertyCode);
    }

    /**
     * @return Strength
     */
    public function getStrengthIncrement()
    {
        return $this->strengthIncrement;
    }

    /**
     * @return Agility
     */
    public function getAgilityIncrement()
    {
        return $this->agilityIncrement;
    }

    /**
     * @return Knack
     */
    public function getKnackIncrement()
    {
        return $this->knackIncrement;
    }

    /**
     * @return Will
     */
    public function getWillIncrement()
    {
        return $this->willIncrement;
    }

    /**
     * @return Intelligence
     */
    public function getIntelligenceIncrement()
    {
        return $this->intelligenceIncrement;
    }

    /**
     * @return Charisma
     */
    public function getCharismaIncrement()
    {
        return $this->charismaIncrement;
    }

    public function getBasePropertyIncrement($propertyCode)
    {
        switch ($propertyCode) {
            case Strength::STRENGTH :
                return $this->getStrengthIncrement();
            case Agility::AGILITY :
                return $this->getAgilityIncrement();
            case Knack::KNACK :
                return $this->getKnackIncrement();
            case Will::WILL :
                return $this->getWillIncrement();
            case Intelligence::INTELLIGENCE :
                return $this->getIntelligenceIncrement();
            case Charisma::CHARISMA :
                return $this->getCharismaIncrement();
            default :
                throw new \LogicException('Unknown property ' . ValueDescriber::describe($propertyCode));
        }
    }

    /**
     * @return Profession
     */
    public function getProfession()
    {
        return $this->profession;
    }
}
