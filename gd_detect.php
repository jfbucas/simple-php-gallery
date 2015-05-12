<?php
$gd_version = gd_version();
echo '<p>Detected GD Version: ' . $gd_version . "\n\n</p>";
if($gd_version != 0)
    echo '<p>Copy/paste the following line into sp_config.php:</p>'
        . '<p>$gd_version = "'
        . $gd_version 
        . '";</p>';
else
    echo "<p>GD was not detected on your server.</p>";
function gd_version() {
   static $gd_version_number = null;
   if ($gd_version_number === null) {
       // Use output buffering to get results from phpinfo()
       // without disturbing the page we're in.  Output
       // buffering is "stackable" so we don't even have to
       // worry about previous or encompassing buffering.
       ob_start();
       phpinfo(8);
       $module_info = ob_get_contents();
       ob_end_clean();
       if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i",
               $module_info,$matches)) {
           $gd_version_number = $matches[1];
       }
       else {
           $gd_version_number = 0;
       }
   }
   return $gd_version_number;
}
?>