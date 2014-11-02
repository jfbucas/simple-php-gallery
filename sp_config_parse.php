<?php
$config_file = fopen('sp_config.dsc','r');
$new_config_file = fopen('sp_config.php','w');

if(!$config_file)
	die("Could not open sp_config.dsc for reading");

if(!$new_config_file)
	die("Could not open sp_config.php for writing");

$new_config_text = '';
$new_desc_file = 'sp_descriptions.ini';

while($line = fgets($config_file))
{
	if(eregi(';;',$line))
	{
		$array = explode(";;", $line);
		$options[strtolower(rtrim($array[0]))] = rtrim($array[1]);
	}	
	else if(eregi('::',$line))
	{
		$array = explode("::", $line);
		if(eregi('\.',$line))
		{
			$descriptions[strtolower($array[0])]['title'] = '';
			$descriptions[strtolower($array[0])]['desc'] = str_replace('"','&quot', rtrim($array[1]));
		}
		else
		{
			$descriptions[strtolower($array[0])]['title'] = str_replace('"','&quot', rtrim($array[1]));
			$descriptions[strtolower($array[0])]['desc'] = '';
		}
	}
}
fclose($config_file);

$new_config_text .= "<?php\n";
$new_config_text .= "//Gallery Title\n";
$new_config_text .= '$title = "' . rtrim($options['title']) . "\";\n\n";
$new_config_text .= "//Thumbnail Settings\n";
$new_config_text .= '$maxthumbwidth = ' . rtrim($options['maxwidth']) . ";\n";
$new_config_text .= '$maxthumbheight = ' . rtrim($options['maxheight']) . ";\n\n";
$new_config_text .= "//Image Resize Settings\n" . '$maxwidth = 0;' . "\n" . '$maxheight = 0;' . "\n\n";
$new_config_text .= "//mod_rewrite Settings\n" . '$modrewrite = ' . rtrim($options['modrewrite']) . ";\n\n";
$new_config_text .= "//Cache Settings\n" . '$cachethumbs = ' . rtrim($options['cachethumbs']) . ";\n";
$new_config_text .= '$cachefolder = "' . rtrim($options['cachefolder']) . "\";\n\n";
$new_config_text .= '$cacheresized = true;' . "\n";
$new_config_text .= '$cacheresizedfolder = "rcache";' . "\n\n";
$new_config_text .= '$precache = true;' . "\n\n";
$new_config_text .= "//Folder Hiding\n" . '$hide_folders[] = "cgi-bin";' . "\n\n";
$new_config_text .= "//Miscellaneous Settings\n";
$new_config_text .= '$showfolderdetails = true;' . "\n";
$new_config_text .= '$showimgtitles = true;' . "\n";
$new_config_text .= '$alignimages = true;' . "\n";
if(gd_version() != 0)
	$new_config_text .= '$gd_version = "' . gd_version() . "\"\n";
$new_config_text .= "?>";

if(!fwrite($new_config_file, $new_config_text))
	die("Unable to write sp_config.php");

echo "<p><strong>sp_config.php</strong> successfully written.</p>";

if(!write_ini_file($new_desc_file, $descriptions))
	die("Unable to write sp_descriptions.ini");

echo "<p><strong>sp_descriptions.ini</strong> successfully written.</p>";

echo "<p>Configuration complete!  You can now delete this file from your server.</p>";

function write_ini_file($path, $assoc_array) {

   foreach ($assoc_array as $key => $item) {
       if (is_array($item)) {
           $content .= "\n[$key]\n";
           foreach ($item as $key2 => $item2) {
               $content .= "$key2 = \"$item2\"\n";
           }       
       } else {
           $content .= "$key = \"$item\"\n";
       }
   }       
  
   if (!$handle = fopen($path, 'w')) {
       return false;
   }
   if (!fwrite($handle, $content)) {
       return false;
   }
   fclose($handle);
   return true;
}
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
       } else {
           $gd_version_number = 0;
       }
   }
   return $gd_version_number;
} 
?>