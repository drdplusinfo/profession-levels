<?php
namespace DrdPlus\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Body\WeightInKg;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\AbstractProfession;
use Granam\Strict\Object\StrictObject;

/**
 * TODO what about classes ProfessionFirstLevel and ProfessionNextLevel ?
 */
abstract class ProfessionLevel extends StrictObject
{

    const PROPERTY_FIRST_LEVEL_MODIFIER = +1;
    const MAXIMUM_LEVEL = 20;
    const MIN_NEXT_LEVEL_PROPERTY_MODIFIER = 0;
    const MAX_NEXT_LEVEL_PROPERTY_MODIFIER = 1;

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
     * @var AbstractProfession
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

    /**
     * @var WeightInKg
     *
     * @ORM\Column(type="weight_in_kg")
     */
    private $weightInKgIncrement;

    protected function __construct(
        AbstractProfession $profession,
        LevelRank $levelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        WeightInKg $weightInKgIncrement,
        \DateTimeImmutable $levelUpAt = null
    )
    {
        $this->profession = $profession;
        $this->checkLevelRank($levelRank);
        $this->levelRank = $levelRank;
        $this->checkPropertyIncrement($strengthIncrement, $levelRank);
        $this->strengthIncrement = $strengthIncrement;
        $this->checkPropertyIncrement($agilityIncrement, $levelRank);
        $this->agilityIncrement = $agilityIncrement;
        $this->checkPropertyIncrement($knackIncrement, $levelRank);
        $this->knackIncrement = $knackIncrement;
        $this->checkPropertyIncrement($willIncrement, $levelRank);
        $this->willIncrement = $willIncrement;
        $this->checkPropertyIncrement($intelligenceIncrement, $levelRank);
        $this->intelligenceIncrement = $intelligenceIncrement;
        $this->checkPropertyIncrement($charismaIncrement, $levelRank);
        $this->charismaIncrement = $charismaIncrement;
        $this->checkWeightIncrement($weightInKgIncrement, $levelRank);
        $this->weightInKgIncrement = $weightInKgIncrement;
        $this->levelUpAt = $levelUpAt ?: new \DateTimeImmutable();
    }

    private function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() > self::MAXIMUM_LEVEL) {
            throw new \LogicException(
                "Level can not be greater than " . self::MAXIMUM_LEVEL . ", got {$levelRank->getValue()}"
            );
        }

    }

    private function checkPropertyIncrement(BaseProperty $property, LevelRank $levelRank)
    {
        if ($levelRank->getValue() === 1) {
            $this->checkPropertyFirstLevelIncrement($property);
        } else {
            $this->checkNextLevelPropertyIncrement($property);
        }
    }

    /**
     * Its only the increment based on first level of specific profession.
     * There are other increments like race, size etc., solved in
     * @see \DrdPlus\Cave\UnitBundle\Person\Attributes\Properties\FirstLevelProperties
     *
     * @param BaseProperty $property
     */
    private function checkPropertyFirstLevelIncrement(BaseProperty $property)
    {
        if ($property->getValue() !== $this->getPropertyFirstLevelModifier($property->getCode())) {
            throw new \LogicException(
                "On first level has to be the property {$property->getCode()} of value {$this->getPropertyFirstLevelModifier($property->getCode())}"
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

    private function checkWeightIncrement(WeightInKg $weightInKg, LevelRank $levelRank)
    {
        if ($levelRank->getValue() > 1 && $weightInKg->getValue() !== 0) {
            throw new \LogicException(
                "Only first level can change weight. Given {$weightInKg->getValue()} kg weight change on level {$levelRank->getValue()}"
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
     *
     * @return int
     */
    private function getPropertyFirstLevelModifier($propertyCode)
    {
        return $this->isPrimaryProperty($propertyCode)
            ? self::PROPERTY_FIRST_LEVEL_MODIFIER
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
        return $this->getProfession()->isPrimaryProperty($propertyCode);
    }

    /**
     * Get strength increment
     *
     * @return Strength
     */
    public function getStrengthIncrement()
    {
        return $this->strengthIncrement;
    }

    /**
     * Get agility increment
     *
     * @return Agility
     */
    public function getAgilityIncrement()
    {
        return $this->agilityIncrement;
    }

    /**
     * Get charisma increment
     *
     * @return Charisma
     */
    public function getCharismaIncrement()
    {
        return $this->charismaIncrement;
    }

    /**
     * Get intelligence increment
     *
     * @return Intelligence
     */
    public function getIntelligenceIncrement()
    {
        return $this->intelligenceIncrement;
    }

    /**
     * Get knack increment
     *
     * @return Knack
     */
    public function getKnackIncrement()
    {
        return $this->knackIncrement;
    }

    /**
     * Get will increment
     *
     * @return Will
     */
    public function getWillIncrement()
    {
        return $this->willIncrement;
    }

    /**
     * Get will increment
     *
     * @return WeightInKg
     */
    public function getWeightInKgIncrement()
    {
        return $this->weightInKgIncrement;
    }

    /**
     * @return AbstractProfession
     */
    public function getProfession()
    {
        return $this->profession;
    }
}
