<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\FLIR;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class CalculatedScaleMin extends AbstractTag
{

    protected $Id = 684;

    protected $Name = 'CalculatedScaleMin';

    protected $FullName = 'FLIR::FPF';

    protected $GroupName = 'FLIR';

    protected $g0 = 'FLIR';

    protected $g1 = 'FLIR';

    protected $g2 = 'Image';

    protected $Type = 'float';

    protected $Writable = false;

    protected $Description = 'Calculated Scale Min';

}
