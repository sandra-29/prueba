<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Leaf;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class StdOutputFileType extends AbstractTag
{

    protected $Id = 'SaveObj_std_output_file_type';

    protected $Name = 'StdOutputFileType';

    protected $FullName = 'Leaf::SaveSetup';

    protected $GroupName = 'Leaf';

    protected $g0 = 'Leaf';

    protected $g1 = 'Leaf';

    protected $g2 = 'Other';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Std Output File Type';

}
