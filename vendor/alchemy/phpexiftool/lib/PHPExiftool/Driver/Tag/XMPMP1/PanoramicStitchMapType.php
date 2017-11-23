<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPMP1;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class PanoramicStitchMapType extends AbstractTag
{

    protected $Id = 'PanoramicStitchMapType';

    protected $Name = 'PanoramicStitchMapType';

    protected $FullName = 'Microsoft::MP1';

    protected $GroupName = 'XMP-MP1';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-MP1';

    protected $g2 = 'Image';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'Panoramic Stitch Map Type';

    protected $Values = array(
        'Horizontal-Cylindrical' => array(
            'Id' => 'Horizontal-Cylindrical',
            'Label' => 'Horizontal Cylindrical',
        ),
        'Horizontal-Spherical' => array(
            'Id' => 'Horizontal-Spherical',
            'Label' => 'Horizontal Spherical',
        ),
        'Perspective' => array(
            'Id' => 'Perspective',
            'Label' => 'Perspective',
        ),
        'Vertical-Cylindrical' => array(
            'Id' => 'Vertical-Cylindrical',
            'Label' => 'Vertical Cylindrical',
        ),
        'Vertical-Spherical' => array(
            'Id' => 'Vertical-Spherical',
            'Label' => 'Vertical Spherical',
        ),
    );

}
