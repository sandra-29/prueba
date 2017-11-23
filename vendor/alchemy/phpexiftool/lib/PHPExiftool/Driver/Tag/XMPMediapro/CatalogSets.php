<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPMediapro;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class CatalogSets extends AbstractTag
{

    protected $Id = 'CatalogSets';

    protected $Name = 'CatalogSets';

    protected $FullName = 'XMP::MediaPro';

    protected $GroupName = 'XMP-mediapro';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-mediapro';

    protected $g2 = 'Image';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'Catalog Sets';

    protected $flag_List = true;

    protected $flag_Bag = true;

}
