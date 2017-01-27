<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use Doctrineum\Entity\Entity;
use DrdPlus\Codes\Properties\PropertyCode;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use DrdPlus\Professions\Profession;
use Granam\Strict\Object\StrictObject;
use Granam\Tools\ValueDescriber;

/** @noinspection SingletonFactoryPatternViolationInspection
 * @ORM\MappedSuperclass()
 */
abstract class ProfessionLevel extends StrictObject implements Entity
{

    /**
     * @var integer
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Profession
     * @ORM\Column(type="profession")
     */
    private $profession;

    /**
     * @var LevelRank
     * @ORM\Column(type="level_rank")
     */
    private $levelRank;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $levelUpAt;

    /**
     * @var Strength
     * @ORM\Column(type="strength")
     */
    private $strengthIncrement;

    /**
     * @var Agility
     * @ORM\Column(type="agility")
     */
    private $agilityIncrement;

    /**
     * @var Knack
     * @ORM\Column(type="knack")
     */
    private $knackIncrement;

    /**
     * @var Will
     * @ORM\Column(type="will")
     */
    private $willIncrement;

    /**
     * @var Intelligence
     * @ORM\Column(type="intelligence")
     */
    private $intelligenceIncrement;

    /**
     * @var Charisma
     * @ORM\Column(type="charisma")
     */
    private $charismaIncrement;

    /**
     * @param Profession $profession
     * @param LevelRank $levelRank
     * @param Strength $strengthIncrement
     * @param Agility $agilityIncrement
     * @param Knack $knackIncrement
     * @param Will $willIncrement
     * @param Intelligence $intelligenceIncrement
     * @param Charisma $charismaIncrement
     * @param \DateTimeImmutable|null $levelUpAt
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     */
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
        $this->checkPropertyIncrement($strengthIncrement, $profession);
        $this->checkPropertyIncrement($agilityIncrement, $profession);
        $this->checkPropertyIncrement($knackIncrement, $profession);
        $this->checkPropertyIncrement($willIncrement, $profession);
        $this->checkPropertyIncrement($intelligenceIncrement, $profession);
        $this->checkPropertyIncrement($charismaIncrement, $profession);
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

    /**
     * @param LevelRank $levelRank
     */
    abstract protected function checkLevelRank(LevelRank $levelRank);

    /**
     * @param LevelRank $levelRank
     * @param Strength $strength
     * @param Agility $agility
     * @param Knack $knack
     * @param Will $will
     * @param Intelligence $intelligence
     * @param Charisma $charisma
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     */
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
        if ($levelRank->isNextLevel()) { // note: first level properties are covered by one-by-one tests
            $sumOfProperties = $this->sumProperties($strength, $agility, $knack, $will, $intelligence, $charisma);
            if ($sumOfProperties !== $this->getExpectedSumOfNextLevelProperties()) {
                throw new Exceptions\InvalidNextLevelPropertiesSum(
                    "Sum of {$levelRank->getValue()}. level properties should be "
                    . $this->getExpectedSumOfNextLevelProperties()
                    . ', got ' . $sumOfProperties
                );
            }
        }
    }

    const PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;
    const SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM = 1;

    /**
     * @return int
     */
    private function getExpectedSumOfNextLevelProperties()
    {
        return static::PRIMARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM + static::SECONDARY_PROPERTY_NEXT_LEVEL_INCREMENT_SUM;
    }

    /**
     * @param Strength $strengthIncrement
     * @param Agility $agilityIncrement
     * @param Knack $knackIncrement
     * @param Will $willIncrement
     * @param Intelligence $intelligenceIncrement
     * @param Charisma $charismaIncrement
     * @return int
     */
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

    /**
     * @param BaseProperty $baseProperty
     * @param Profession $profession
     */
    abstract protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession);

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

    /** @return bool */
    public function isFirstLevel()
    {
        return $this->getLevelRank()->getValue() === 1;
    }

    /** @return bool */
    public function isNextLevel()
    {
        return $this->getLevelRank()->getValue() > 1;
    }

    /**
     * @param PropertyCode $propertyCode
     * @return bool
     */
    public function isPrimaryProperty(PropertyCode $propertyCode)
    {
        return static::isProfessionPrimaryProperty($this->getProfession(), $propertyCode);
    }

    /**
     * @param Profession $profession
     * @param PropertyCode $propertyCode
     * @return bool
     */
    protected static function isProfessionPrimaryProperty(Profession $profession, PropertyCode $propertyCode)
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
     * @param PropertyCode $propertyCode
     * @return Agility|Charisma|Intelligence|Knack|Strength|Will
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\UnknownBaseProperty
     */
    public function getBasePropertyIncrement(PropertyCode $propertyCode)
    {
        switch ($propertyCode->getValue()) {
            case PropertyCode::STRENGTH :
                return $this->getStrengthIncrement();
            case PropertyCode::AGILITY :
                return $this->getAgilityIncrement();
            case PropertyCode::KNACK :
                return $this->getKnackIncrement();
            case PropertyCode::WILL :
                return $this->getWillIncrement();
            case PropertyCode::INTELLIGENCE :
                return $this->getIntelligenceIncrement();
            case PropertyCode::CHARISMA  :
                return $this->getCharismaIncrement();
            default :
                throw new Exceptions\UnknownBaseProperty(
                    'Unknown property ' . ValueDescriber::describe($propertyCode)
                );
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