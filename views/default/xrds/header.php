<?php

        /**
         * Adds metatags to identify OpenID server
         * 
         * @package ElggOpenID
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author Kevin Jardine <kevin@radagast.biz>
         * @copyright Curverider Ltd 2008-2009
         * @link http://elgg.org/
         * 
         */

$url = current_page_url();
$site_url = elgg_get_site_url();

if (substr_count($url,'?')) {
        $url .= "&view=xrds";
} else {
        $url .= "?view=xrds";
}

?>
        <link rel="lrdd" href="<?php echo $url; ?>" />
        <meta http-equiv="X-XRDS-Location" content="<?php echo $url; ?>" />
