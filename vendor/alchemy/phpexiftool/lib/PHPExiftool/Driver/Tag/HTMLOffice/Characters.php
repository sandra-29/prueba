<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\HTMLOffice;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class Characters extends AbstractTag
{

    protected $Id = 'Characters';

    protected $Name = 'Characters';

    protected $FullName = 'HTML::Office';

    protected $GroupName = 'HTML-office';

    protected $g0 = 'HTML';

    protected $g1 = 'HTML-office';

    protected $g2 = 'Document';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'Characters';

}
