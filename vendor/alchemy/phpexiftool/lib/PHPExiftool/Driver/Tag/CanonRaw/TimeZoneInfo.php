<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\CanonRaw;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class TimeZoneInfo extends AbstractTag
{

    protected $Id = 2;

    protected $Name = 'TimeZoneInfo';

    protected $FullName = 'CanonRaw::TimeStamp';

    protected $GroupName = 'CanonRaw';

    protected $g0 = 'MakerNotes';

    protected $g1 = 'CanonRaw';

    protected $g2 = 'Time';

    protected $Type = 'int32u';

    protected $Writable = true;

    protected $Description = 'Time Zone Info';

    protected $flag_Permanent = true;

}
