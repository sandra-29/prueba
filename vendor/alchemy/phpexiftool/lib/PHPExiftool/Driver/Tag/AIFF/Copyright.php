<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\AIFF;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Copyright extends AbstractTag
{

    protected $Id = '(c) ';

    protected $Name = 'Copyright';

    protected $FullName = 'AIFF::Main';

    protected $GroupName = 'AIFF';

    protected $g0 = 'AIFF';

    protected $g1 = 'AIFF';

    protected $g2 = 'Audio';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Copyright';

    protected $local_g2 = 'Author';

}
