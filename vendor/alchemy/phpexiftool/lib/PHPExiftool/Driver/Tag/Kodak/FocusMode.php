<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Kodak;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class FocusMode extends AbstractTag
{

    protected $Id = 56;

    protected $Name = 'FocusMode';

    protected $FullName = 'Kodak::Main';

    protected $GroupName = 'Kodak';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Kodak';

    protected $g2 = 'Camera';

    protected $Type = 'int8u';

    protected $Writable = true;

    protected $Description = 'Focus Mode';

    protected $flag_Permanent = true;

    protected $Values = array(
        0 => array(
            'Id' => 0,
            'Label' => 'Normal',
        ),
        2 => array(
            'Id' => 2,
            'Label' => 'Macro',
        ),
    );

}
