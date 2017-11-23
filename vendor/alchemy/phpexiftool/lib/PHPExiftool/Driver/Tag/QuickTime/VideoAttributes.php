<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\QuickTime;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class VideoAttributes extends AbstractTag
{

    protected $Id = 4;

    protected $Name = 'VideoAttributes';

    protected $FullName = 'QuickTime::VideoProf';

    protected $GroupName = 'QuickTime';

    protected $g0 = 'QuickTime';

    protected $g1 = 'QuickTime';

    protected $g2 = 'Video';

    protected $Type = 'int32u';

    protected $Writable = false;

    protected $Description = 'Video Attributes';

    protected $Values = array(
        1 => array(
            'Id' => 1,
            'Label' => 'Encrypted',
        ),
        2 => array(
            'Id' => 2,
            'Label' => 'Variable bitrate',
        ),
        4 => array(
            'Id' => 4,
            'Label' => 'Variable frame rate',
        ),
        8 => array(
            'Id' => 8,
            'Label' => 'Interlaced',
        ),
    );

}
