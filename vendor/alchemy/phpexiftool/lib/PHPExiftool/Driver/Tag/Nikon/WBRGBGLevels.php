<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Nikon;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class WBRGBGLevels extends AbstractTag
{

    protected $Id = 0;

    protected $Name = 'WB_RGBGLevels';

    protected $FullName = 'Nikon::ColorBalance3';

    protected $GroupName = 'Nikon';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Nikon';

    protected $g2 = 'Camera';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'WB RGBG Levels';

    protected $flag_Permanent = true;

    protected $flag_Unsafe = true;

    protected $MaxLength = 4;

}
