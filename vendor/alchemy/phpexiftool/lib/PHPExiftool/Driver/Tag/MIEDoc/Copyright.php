<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\MIEDoc;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Copyright extends AbstractTag
{

    protected $Id = 'Copyright';

    protected $Name = 'Copyright';

    protected $FullName = 'MIE::Doc';

    protected $GroupName = 'MIE-Doc';

    protected $g0 = 'MIE';

    protected $g1 = 'MIE-Doc';

    protected $g2 = 'Document';

    protected $Type = 'string';

    protected $Writable = true;

    protected $Description = 'Copyright';

    protected $local_g2 = 'Author';

}
