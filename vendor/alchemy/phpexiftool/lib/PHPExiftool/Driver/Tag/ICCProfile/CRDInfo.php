<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\ICCProfile;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class CRDInfo extends AbstractTag
{

    protected $Id = 'crdi';

    protected $Name = 'CRDInfo';

    protected $FullName = 'ICC_Profile::Main';

    protected $GroupName = 'ICC_Profile';

    protected $g0 = 'ICC_Profile';

    protected $g1 = 'ICC_Profile';

    protected $g2 = 'Image';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'CRD Info';

}
