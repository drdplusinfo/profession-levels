<?php
namespace DrdPlus\ProfessionLevels;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Tests\ProfessionLevels\AbstractTestOfProfessionLevel;

class ThiefLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Agility::AGILITY, Knack::KNACK];
    }

}
