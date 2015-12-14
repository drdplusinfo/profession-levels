<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;


class TheurgistLevelTest extends AbstractTestOfProfessionLevel
{

    /**
     * @return string[]
     */
    protected function getPrimaryProperties()
    {
        return [Intelligence::INTELLIGENCE, Charisma::CHARISMA];
    }

}
