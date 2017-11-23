<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Olympus;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class RawDevEngine extends AbstractTag
{

    protected $Id = 'mixed';

    protected $Name = 'RawDevEngine';

    protected $FullName = 'mixed';

    protected $GroupName = 'Olympus';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Olympus';

    protected $g2 = 'Camera';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Raw Dev Engine';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'High Speed',
        ),
        1 => array(
            'Id' => 1,
            'Label' => 'High Function',
        ),
        2 => array(
            'Id' => 2,
            'Label' => 'Advanced High Speed',
        ),
        3 => array(
            'Id' => 3,
            'Label' => 'Advanced High Function',
        ),
        4 => array(
            'Id' => 0,
            'Label' => 'High Speed',
        ),
        5 => array(
            'Id' => 1,
            'Label' => 'High Function',
        ),
    );

}
