<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Tag\Torrent;

use JMS\Serializer\Annotation\ExclusionPolicy;
use PHPExiftool\Driver\AbstractTag;

/**
 * @ExclusionPolicy("all")
 */
class File1PathUTF8 extends AbstractTag
{

    protected $Id = 'path.utf-8';

    protected $Name = 'File1PathUTF-8';

    protected $FullName = 'Torrent::Files';

    protected $GroupName = 'Torrent';

    protected $g0 = 'Torrent';

    protected $g1 = 'Torrent';

    protected $g2 = 'Document';

    protected $Type = '?';

    protected $Writable = false;

    protected $Description = 'File 1 Path UTF-8';

}
