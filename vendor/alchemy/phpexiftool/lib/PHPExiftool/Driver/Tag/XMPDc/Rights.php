<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPDc;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Rights extends AbstractTag
{

    protected $Id = 'rights';

    protected $Name = 'Rights';

    protected $FullName = 'XMP::dc';

    protected $GroupName = 'XMP-dc';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-dc';

    protected $g2 = 'Other';

    protected $Type = 'lang-alt';

    protected $Writable = true;

    protected $Description = 'Rights';

    protected $local_g2 = 'Author';

}
