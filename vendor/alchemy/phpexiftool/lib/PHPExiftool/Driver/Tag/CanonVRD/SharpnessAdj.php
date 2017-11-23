<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\CanonVRD;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class SharpnessAdj extends AbstractTag
{

    protected $Id = 'mixed';

    protected $Name = 'SharpnessAdj';

    protected $FullName = 'mixed';

    protected $GroupName = 'CanonVRD';

    protected $g0 = 'CanonVRD';

    protected $g1 = 'CanonVRD';

    protected $g2 = 'Image';

    protected $Type = 'mixed';

    protected $Writable = true;

    protected $Description = 'Sharpness Adj';

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'Sharpness',
        ),
        1 => array(
            'Id' => 1,
            'Label' => 'Unsharp Mask',
        ),
    );

}
