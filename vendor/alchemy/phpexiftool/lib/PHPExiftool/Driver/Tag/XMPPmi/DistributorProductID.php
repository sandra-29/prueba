<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\XMPPmi;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class DistributorProductID extends AbstractTag
{

    protected $Id = 'distributorProductID';

    protected $Name = 'DistributorProductID';

    protected $FullName = 'XMP::pmi';

    protected $GroupName = 'XMP-pmi';

    protected $g0 = 'XMP';

    protected $g1 = 'XMP-pmi';

    protected $g2 = 'Image';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'Distributor Product ID';

    protected $flag_Avoid = true;

}
