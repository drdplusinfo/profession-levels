<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\Knack;


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
