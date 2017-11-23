<?php

/*
 * This file is part of Media-Alchemyst.
 *
 * (c) Alchemy <dev.team@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MediaAlchemyst\Specification;

interface SpecificationInterface
{
    const TYPE_IMAGE = 'image';
    const TYPE_ANIMATION = 'animation';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_SWF = 'swf';

    public function getType();
}
