<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\SigmaRaw;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ExposureProgram extends AbstractTag
{

    protected $Id = 'PMODE';

    protected $Name = 'ExposureProgram';

    protected $FullName = 'SigmaRaw::Properties';

    protected $GroupName = 'SigmaRaw';

    protected $g0 = 'SigmaRaw';

    protected $g1 = 'SigmaRaw';

    protected $g2 = 'Camera';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Exposure Program';

    protected $Values = array(
        'A' => array(
            'Id' => 'A',
            'Label' => 'Aperture Priority',
        ),
        'M' => array(
            'Id' => 'M',
            'Label' => 'Manual',
        ),
        'P' => array(
            'Id' => 'P',
            'Label' => 'Program',
        ),
        'S' => array(
            'Id' => 'S',
            'Label' => 'Shutter Priority',
        ),
    );

}
