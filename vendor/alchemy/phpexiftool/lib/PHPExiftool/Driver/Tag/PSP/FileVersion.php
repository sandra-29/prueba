<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\PSP;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class FileVersion extends AbstractTag
{

    protected $Id = 'FileVersion';

    protected $Name = 'FileVersion';

    protected $FullName = 'PSP::Main';

    protected $GroupName = 'PSP';

    protected $g0 = 'PSP';

    protected $g1 = 'PSP';

    protected $g2 = 'Image';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'File Version';

}
