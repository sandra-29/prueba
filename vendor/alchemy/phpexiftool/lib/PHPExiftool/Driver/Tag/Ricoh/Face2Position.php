<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Ricoh;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Face2Position extends AbstractTag
{

    protected $Id = 200;

    protected $Name = 'Face2Position';

    protected $FullName = 'Ricoh::FaceInfo';

    protected $GroupName = 'Ricoh';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'Ricoh';

    protected $g2 = 'Camera';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Face 2 Position';

    protected $flag_Permanent = true;

    protected $MaxLength = 4;

}
