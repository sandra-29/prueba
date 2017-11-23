<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Utils;

/**
 * Class ChamiloApi
 * @package Chamilo\CoreBundle\Component
 */
class ChamiloApi
{
    private static $configuration;

    /**
     * ChamiloApi constructor.
     * @param $configuration
     */
    public function __construct(array $configuration)
    {
        self::$configuration = $configuration;
    }

    /**
     * @return array
     */
    public static function getConfigurationArray()
    {
        return self::$configuration;
    }

    /**
     * @param string $variable
     * @return bool|string
     */
    public static function getConfigurationValue($variable)
    {
        $configuration = self::getConfigurationArray();
        if (array_key_exists($variable, $configuration)) {
            return $configuration[$variable];
        }

        return false;
    }


    /**
     * Returns an array of resolutions that can be used for the conversion of documents to images
     * @return array
     */
    public static function getDocumentConversionSizes()
    {
        return array(
            '540x405' => '540x405 (3/4)',
            '640x480' => '640x480 (3/4)',
            '720x540' => '720x540 (3/4)',
            '800x600' => '800x600 (3/4)',
            '1024x576' => '1024x576 (16/9)',
            '1024x768' => '1000x750 (3/4)',
            '1280x720' => '1280x720 (16/9)',
            '1280x860' => '1280x960 (3/4)',
            '1400x1050' => '1400x1050 (3/4)',
            '1600x900' => '1600x900 (16/9)',
        );
    }

    /**
     * Get the platform logo path
     * @return null|string
     */
    public static function getWebPlatformLogoPath()
    {
        $theme = api_get_visual_theme();
        $accessUrlId = api_get_current_access_url_id();
        $customLogoPath = "themes/$theme/images/header-logo-custom$accessUrlId.png";

        if (file_exists(api_get_path(SYS_PUBLIC_PATH) . "css/$customLogoPath")) {
            return api_get_path(WEB_CSS_PATH) . $customLogoPath;
        }

        $originalLogoPath = "themes/$theme/images/header-logo.png";

        if (file_exists(api_get_path(SYS_CSS_PATH) . $originalLogoPath)) {
            return api_get_path(WEB_CSS_PATH) . $originalLogoPath;
        }

        return null;
    }

    /**
     * Get the platform logo.
     * Return a <img> if the logo image exists. Otherwise return a <h2> with the institution name.
     * @param array $imageAttributes Optional.
     * @return string
     */
    public static function getPlatformLogo($imageAttributes = [])
    {
        $logoPath = self::getWebPlatformLogoPath();
        $insitution = api_get_setting('Institution');
        $insitutionUrl = api_get_setting('InstitutionUrl');
        $siteName = api_get_setting('siteName');

        if ($logoPath === null) {
            $headerLogo = \Display::url($siteName, api_get_path(WEB_PATH) . 'index.php');

            if (!empty($insitutionUrl) && !empty($insitution)) {
                $headerLogo .= ' - ' . \Display::url($insitution, $insitutionUrl);
            }

            $courseInfo = api_get_course_info();

            if (isset($courseInfo['extLink']) && !empty($courseInfo['extLink']['name'])) {
                $headerLogo .= '<span class="extLinkSeparator"> - </span>';

                if (!empty($courseInfo['extLink']['url'])) {
                    $headerLogo .= \Display::url(
                        $courseInfo['extLink']['name'],
                        $courseInfo['extLink']['url'],
                        ['class' => 'extLink']
                    );
                } else if (!empty($courseInfo['extLink']['url'])) {
                    $headerLogo .= $courseInfo['extLink']['url'];
                }
            }

            return \Display::tag('h2', $headerLogo, ['class' => 'text-left']);
        }

        $image = \Display::img($logoPath, $insitution, $imageAttributes);

        return \Display::url($image, api_get_path(WEB_PATH) . 'index.php');
    }

    /**
     * Like strip_tags(), but leaves an additional space and removes only the given tags
     * @param string $string
     * @param array $tags Tags to be removed
     * @return  string The original string without the given tags
     */
    public static function stripGivenTags($string, $tags)
    {
        foreach ($tags as $tag) {
            $string2 = preg_replace('#</' . $tag . '[^>]*>#i', ' ', $string);
            if ($string2 != $string) {
                $string = preg_replace('/<' . $tag . '[^>]*>/i', ' ', $string2);
            }
        }
        return $string;
    }
}
