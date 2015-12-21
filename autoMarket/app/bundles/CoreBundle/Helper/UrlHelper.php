<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Helper;

/**
 * Class UrlHelper
 */
class UrlHelper
{
    /**
     * @param $rel
     *
     * @return string
     */
    public static function rel2abs($rel)
    {
        $path = $host = $scheme = "";

        $base = 'http';
        if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $base .= "s";
        }
        $base .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $base .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $base .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        $base = str_replace('/index_dev.php', '', $base);
        $base = str_replace('/index.php', '', $base);

        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        extract(parse_url($base));

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL // with port number if exists */
        if (parse_url($base, PHP_URL_PORT) != ''){
            $abs = "$host:".parse_url($base, PHP_URL_PORT)."$path/$rel";
        }else{
            $abs = "$host$path/$rel";
        }
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }
}
