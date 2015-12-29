<?php
namespace DrdPlus\Person\ProfessionLevels;

use Doctrine\ORM\Mapping as ORM;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use \DrdPlus\Professions\Theurgist;

/**
 * Theurgist
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TheurgistLevel extends ProfessionLevel
{

    /**
     * Inner link, used by Doctrine only
     * @var ProfessionLevels
     *
     * @ORM\ManyToOne(targetEntity="ProfessionLevels", inversedBy="theurgistLevels")
     */
    protected $professionLevels;

    public static function createFirstLevel(Theurgist $theurgist, \DateTimeImmutable $levelUpAt = null)
    {
        return parent::createFirstLevelFor($theurgist, $levelUpAt);
    }

    public function __construct(
        Theurgist $theurgist,
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
        parent::__construct(
            $theurgist,
            $levelRank,
            $strengthIncrement,
            $agilityIncrement,
            $knackIncrement,
            $willIncrement,
            $intelligenceIncrement,
            $charismaIncrement,
            $levelUpAt
        );
    }

}