<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\SonyIDC;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ToneCurveRedX extends AbstractTag
{

    protected $Id = 36865;

    protected $Name = 'ToneCurveRedX';

    protected $FullName = 'SonyIDC::Main';

    protected $GroupName = 'SonyIDC';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'SonyIDC';

    protected $g2 = 'Image';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Tone Curve Red X';

    protected $flag_Permanent = true;

}
