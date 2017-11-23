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
class ToneCurveInterpolation extends AbstractTag
{

    protected $Id = 345;

    protected $Name = 'ToneCurveInterpolation';

    protected $FullName = 'CanonVRD::Ver1';

    protected $GroupName = 'CanonVRD';

    protected $g0 = 'CanonVRD';

    protected $g1 = 'CanonVRD';

    protected $g2 = 'Image';

    protected $Type = 'int8u';

    protected $Writable = true;

    protected $Description = 'Tone Curve Interpolation';

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'Curve',
        ),
        1 => array(
            'Id' => 1,
            'Label' => 'Straight',
        ),
    );

}
