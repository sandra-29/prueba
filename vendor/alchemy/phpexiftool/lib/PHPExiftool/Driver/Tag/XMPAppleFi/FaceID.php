<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPAppleFi;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class FaceID extends AbstractTag
{

    protected $Id = 'FaceID';

    protected $Name = 'FaceID';

    protected $FullName = 'XMP::apple_fi';

    protected $GroupName = 'XMP-apple-fi';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-apple-fi';

    protected $g2 = 'Image';

    protected $Type = 'integer';

    protected $Writable = true;

    protected $Description = 'Face ID';

}
