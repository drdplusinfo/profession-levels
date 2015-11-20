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

    public static function createFirstLevel(
        AbstractProfession $profession,
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
        AbstractProfession $profession,
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
        $this->levelUpAt = $levelUpAt ?: new \DateTimeImmutable();
    }

    private function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() > self::MAXIMUM_LEVEL) {
            throw new Exceptions\MaximumLevelExceeded(
                "Level can not be greater than " . self::MAXIMUM_LEVEL . ", got {$levelRank->getValue()}"
            );
        }

    }

    private function checkPropertyIncrement(BaseProperty $property, LevelRank $levelRank)
    {
        if ($levelRank->isFirstLevel()) {
            $this->checkPropertyFirstLevelIncrement($property);
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
     */
    private function checkPropertyFirstLevelIncrement(BaseProperty $property)
    {
        $propertyFirstLevelModifier = static::getBasePropertyFirstLevelModifier(
            $property->getCode(),
            $this->getProfession()
        );
        if ($property->getValue() !== $propertyFirstLevelModifier) {
            throw new \LogicException(
                "On first level has to be the property {$property->getCode()} of value {$propertyFirstLevelModifier}"
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
     * @param AbstractProfession $profession
     *
     * @return int
     */
    private static function getBasePropertyFirstLevelModifier($propertyCode, AbstractProfession $profession)
    {
        return static::isProfessionPrimaryProperty($profession, $propertyCode)
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
        return static::isProfessionPrimaryProperty($this->getProfession(), $propertyCode);
    }

    private static function isProfessionPrimaryProperty(AbstractProfession $profession, $propertyCode)
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

    /**
     * @return AbstractProfession
     */
    public function getProfession()
    {
        return $this->profession;
    }
}
