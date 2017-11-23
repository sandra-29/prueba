<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPDex;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class CRC32 extends AbstractTag
{

    protected $Id = 'crc32';

    protected $Name = 'CRC32';

    protected $FullName = 'XMP::dex';

    protected $GroupName = 'XMP-dex';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-dex';

    protected $g2 = 'Image';

    protected $Type = 'integer';

    protected $Writable = true;

    protected $Description = 'CRC32';

}
