<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;


class RangerLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Strength::STRENGTH, Knack::KNACK];
    }


}
