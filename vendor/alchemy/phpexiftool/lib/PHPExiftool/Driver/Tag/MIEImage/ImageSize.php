<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\MIEImage;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ImageSize extends AbstractTag
{

    protected $Id = 'ImageSize';

    protected $Name = 'ImageSize';

    protected $FullName = 'MIE::Image';

    protected $GroupName = 'MIE-Image';

    protected $g0 = 'MIE';

    protected $g1 = 'MIE-Image';

    protected $g2 = 'Image';

    protected $Type = 'int16u';

    protected $Writable = true;

    protected $Description = 'Image Size';

}
