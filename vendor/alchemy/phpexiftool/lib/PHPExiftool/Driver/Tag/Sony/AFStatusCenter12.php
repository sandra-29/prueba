<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Sony;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class AFStatusCenter12 extends AbstractTag
{

    protected $Id = 56;

    protected $Name = 'AFStatusCenter-12';

    protected $FullName = 'Sony::CameraInfo';

    protected $GroupName = 'Sony';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Sony';

    protected $g2 = 'Camera';

    protected $Type = 'int16s';

    protected $Writable = true;

    protected $Description = 'AF Status Center-12';

    protected $flag_Permanent = true;

    protected $Values = array(
        '-32768' => array(
            'Id' => '-32768',
            'Label' => 'Out of Focus',
        ),
        0 => array(
            'Id' => 0,
            'Label' => 'In Focus',
        ),
    );

}
