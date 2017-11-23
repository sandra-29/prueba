<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\ASF;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Picture extends AbstractTag
{

    protected $Id = 3;

    protected $Name = 'Picture';

    protected $FullName = 'ASF::Picture';

    protected $GroupName = 'ASF';

    protected $g0 = 'ASF';

    protected $g1 = 'ASF';

    protected $g2 = 'Image';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Picture';

    protected $local_g2 = 'Preview';

    protected $flag_Binary = true;

}
