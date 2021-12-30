<?php
/*
 * WALTR Version 1.0.1 - Website Active Link Reporting Tool
 * R. Seaverns 2021 
 * Generate a simple web crawler (bot).
 * collect & store data from a single URL at a time.
 * Domain Name Only - No http:// or www. or ftp://
 * Does Not Follow External Links
 * Does Not Obey robots.txt
 * Creates & Searches /data folder for files.
 * Output each line of each found file as link.
 * Default = single file.
 * Error Reporting - ON (Default)
*/
?>

<style>
body {
    background-color:#0c0c0c;
    color:#fff;
    line-height:150%;
    margin:2%;
    font-family:Roboto;
    font-weight:600;
}

a, a:visited {
    text-decoration:none;
    color:#fff;
}

a:hover {
    color:lime;
}
</style>
<h1>WALTR - Website Active Link Reporting Tool</h1>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check To See If We Have A Clean URL To Work With - See: #### E O F ####
if(isset($_POST["submit"])){

// Set Functions - Create WALTR - Collect Links - Kill WALTR (It's a bot's life..)
// Check Data Folder Beforehand - If Exists $filename Display It, If Not Get It.
function checkForDuplicateFiles() {
    $DATA = "data/";
    $UserInput = htmlspecialchars($_POST["URL"]);
    $clutter = array('http://', 'www', 'https://', 'ftp://', 'http://www', 'https://www'); // No Effect
    $clean = str_replace($clutter, '', $UserInput); // No Effect
    $URL = trim($clean);
    $filename = $DATA.$URL;

    if (file_exists($filename)) {
        GetBotData(); // Display The Data.
    } else {
        Spawn_WALTR(); // Go Fetch It!
 }
}

// Create WALTR - Generate Disposeable Bot File.
function Spawn_WALTR() {
    $WALTR = 'WALTR.bot';
    $botCode = '#!/bin/bash
    DIR="data"
    mkdir -p $DIR
    while getopts u: flag
     do
    case "${flag}" in
        u) URL=${OPTARG};;
    esac
     done
    wget -q $URL -O - | \\
    tr "\t\r\n\'" \'   "\' | \\
    grep -i -o \'<a[^>]\+href[ ]*=[ \t]*"\(ht\|f\)tps\?:[^"]\+"\' | \\
    sed -e \'s/^.*"\([^"]\+\)".*$/\1/g\' | awk \'!seen[$0]++\' >> $DIR/$URL
    find . -name \'file*\' -size 0 -print0 | xargs -0 rm';

// Write The WALTER File & Set Permissions.
    $genBot = fopen('WALTR.bot', 'a');
    fwrite($genBot, $botCode);
    chmod($WALTR, 0755);

// Run WALTER & Collect Data From URL
$URL = htmlspecialchars($_POST["URL"]);
    $runbot = shell_exec("sh $WALTR -u $URL");

// Kill WALTR (RIP Bro!)
    unlink ($WALTR);

// Display The Data
    GetBotData();
}

// Display The Data WALTR Has Collected As Links.
function GetBotData() {

// Set The Data Directory.
    $data='data';

// Read DIR For Saved Files
    foreach (new DirectoryIterator($data) as $files) {
        if($files->isDot()) continue;
        $file = $files->getFilename();
        echo '<h2>WebSite: '.$file.'</h2>';

// Output links from file
$inputFile = fopen($data.'/'.$file, "r");
    if ($inputFile) {
        while (($line = fgets($inputFile)) !== false) {
        echo '<a href="'.$line.'" target="_blank" />'.$line.'</a><br>'."\n";
       }
        echo '<hr style="border:2px solid #666;">';
        fclose($inputFile);
        } else {
        echo ' Nothing To See Here Folks!';
        } 
      }
    }

// Display The Form Again.
echo '<form action="" method ="post">';
echo ' <p>URL To Crawl: <input style="margin-left:5px;" type="text" name="URL">&nbsp;&nbsp;&nbsp;&nbsp;<em>Example: some.com</em></p>';
echo ' <p><input type="submit" value="Crawl WebSite" name="submit"></p>';
echo '</form>';
echo '<hr style="border:2px solid #666;">';

// By Default We Want To First Check For Duplicates
    checkForDuplicateFiles();

// If The Form Is Empty.
} else { //Display The Input Form // #### E O F #### ?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method ="post">
 <p>Domain To Crawl: <input style="margin-left:5px;" type="text" name="URL">&nbsp;&nbsp;&nbsp;&nbsp;<em>Example: some.com</em></p>
 <p><input type="submit" value="Crawl WebSite" name="submit"></p>
</form>
 <hr style="border:2px solid #666;">
 <?php } ?>
