<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\PanasonicRaw;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class DistortionN extends AbstractTag
{

    protected $Id = 12;

    protected $Name = 'DistortionN';

    protected $FullName = 'PanasonicRaw::DistortionInfo';

    protected $GroupName = 'PanasonicRaw';

    protected $g0 = 'PanasonicRaw';

    protected $g1 = 'PanasonicRaw';

    protected $g2 = 'Image';

    protected $Type = 'int16s';

    protected $Writable = true;

    protected $Description = 'Distortion N';

}
