<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPPhotoshop;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class ICCProfileName extends AbstractTag
{

    protected $Id = 'ICCProfile';

    protected $Name = 'ICCProfileName';

    protected $FullName = 'XMP::photoshop';

    protected $GroupName = 'XMP-photoshop';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-photoshop';

    protected $g2 = 'Image';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'ICC Profile Name';

}
