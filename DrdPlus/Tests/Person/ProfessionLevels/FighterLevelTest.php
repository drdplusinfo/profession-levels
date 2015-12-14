<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Strength;

class FighterLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Strength::STRENGTH, Agility::AGILITY];
    }

}
