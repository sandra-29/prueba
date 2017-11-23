<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\NikonCapture;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class NoiseReductionMethod extends AbstractTag
{

    protected $Id = 17;

    protected $Name = 'NoiseReductionMethod';

    protected $FullName = 'NikonCapture::NoiseReduction';

    protected $GroupName = 'NikonCapture';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'NikonCapture';

    protected $g2 = 'Image';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Noise Reduction Method';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'Faster',
        ),
        1 => array(
            'Id' => 1,
            'Label' => 'Better Quality',
        ),
        2 => array(
            'Id' => 2,
            'Label' => 'Better Quality 2013',
        ),
    );

}
