<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\FotoStation;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class CropTop extends AbstractTag
{

    protected $Id = 7;

    protected $Name = 'CropTop';

    protected $FullName = 'FotoStation::SoftEdit';

    protected $GroupName = 'FotoStation';

    protected $g0 = 'FotoStation';

    protected $g1 = 'FotoStation';

    protected $g2 = 'Image';

    protected $Type = 'int32s';

    protected $Writable = true;

    protected $Description = 'Crop Top';

}
