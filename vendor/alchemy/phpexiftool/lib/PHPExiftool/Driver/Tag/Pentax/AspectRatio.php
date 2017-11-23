<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Pentax;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class AspectRatio extends AbstractTag
{

    protected $Id = 128;

    protected $Name = 'AspectRatio';

    protected $FullName = 'Pentax::Main';

    protected $GroupName = 'Pentax';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Pentax';

    protected $g2 = 'Camera';

    protected $Type = '?';

    protected $Writable = true;

    protected $Description = 'Aspect Ratio';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => '4:3',
        ),
        1 => array(
            'Id' => 1,
            'Label' => '3:2',
        ),
        2 => array(
            'Id' => 2,
            'Label' => '16:9',
        ),
        3 => array(
            'Id' => 3,
            'Label' => '1:1',
        ),
    );

}
