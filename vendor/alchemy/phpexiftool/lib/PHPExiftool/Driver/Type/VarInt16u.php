<?php

/*
 * This file is part of the PHPExifTool package.
 *
 * (c) Alchemy <support@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPExiftool\Driver\Type;

use PHPExiftool\Driver\AbstractType;

class VarInt16u extends AbstractType
{

    protected $ExiftoolName = 'var_int16u';

    protected $PHPMap = 'int';

}
