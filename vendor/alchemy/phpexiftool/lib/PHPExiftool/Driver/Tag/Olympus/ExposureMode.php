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
class ExposureMode extends AbstractTag
{

    protected $Id = 512;

    protected $Name = 'ExposureMode';

    protected $FullName = 'Olympus::CameraSettings';

    protected $GroupName = 'Olympus';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Olympus';

    protected $g2 = 'Camera';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Exposure Mode';

    protected $flag_Permanent = true;

    protected $Values = array(
        1 => array(
            'Id' => 1,
            'Label' => 'Manual',
        ),
        2 => array(
            'Id' => 2,
            'Label' => 'Program',
        ),
        3 => array(
            'Id' => 3,
            'Label' => 'Aperture-priority AE',
        ),
        4 => array(
            'Id' => 4,
            'Label' => 'Shutter speed priority AE',
        ),
        5 => array(
            'Id' => 5,
            'Label' => 'Program-shift',
        ),
    );

}
