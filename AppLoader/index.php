<?php
/**
 * index.php
 *
 * This file contains the whole thing, so its very easy and fast to get this little helper running
 * on the destination webspace. Just upload this file into an empty folder of your choice with write
 * permissions and open it in your browser. For more informations have a look at the README file in
 * the root folder of this software package. Enjoy this little script!
 *
 *
 * @author       Christian Knerr
 * @version      1.0.1
 * @package      AppLoader
 * @copyright    (c)2018 CBACK Software
 * @link         https://cback.net
 * @license      MIT License
 *
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


class CBACKAppLoader
{
	/**
	 * Downloadable Sources as an Array. You can extend it if you need other tools to download. As long the download
	 * Link points to a zip file it'll work.
	 *
	 * Format of the Array with the downloads in the Main Array: [ AppDisplayName ] [ AppFolderName (must be a valid file system name) ] [ AppDownloadURL ]
	 * @var array $appDownloads
	 * @access private
	 */
	private $appDownloads = array(
		array('phpMyAdmin',  'pma', 'https://files.phpmyadmin.net/phpMyAdmin/4.8.0/phpMyAdmin-4.8.0-all-languages.zip'),
		array('MySQLDumper', 'msd', 'https://github.com/DSB/MySQLDumper/archive/master.zip')
	);

	/**
	 * Name of the working directory
	 * @var string $workingDir
	 * @access private
	 * @static
	 */
	private static $workingDir = '_toolbox';

	/**
	 * Absolute Path of this script
	 * @var string $path
	 * @access private
	 */
	private $path = '';

	/**
	 * Store the full path to the working directory just to save some code later. Var is filled by the constructor
	 * @var string $workingDirPath
	 * @access private
	 */
	private $workingDirPath = '';

	/**
	 * Was the HTML Page Header already displayed? Just a little helper var.
	 * @var bool $headerShown
	 * @access private
	 */
	private $headerShown = false;


	/**
	 * The constructor creates the working directory (if possible) and checks some basic things to
	 * ensure, that the server has everything on board you need to use this script.
	 * @access public
	 */
	public function __construct()
	{
		// store absolute path & path to the working directory
		$this->path				= dirname( __FILE__ );
		$this->workingDirPath	= $this->path . '/' . self::$workingDir;

		// the server needs ZipArchive and curl for this tool to work correctly
		if ( !class_exists('ZipArchive') || !function_exists('curl_init') )
		{
			$this->show_message('CRITICAL ERROR', 'Sorry, you need the support for ZipArchive and curl in your PHP installation to use this script.', 'error', true);
			exit;
		}

		// is the working directory there and writable? If not we try to create it.
		if( is_writable($this->path) )
		{
			if( file_exists($this->workingDirPath) && !is_dir($this->workingDirPath) )
			{
				// Should hopefully never happen but we want to handle all possibilities
				$this->show_message('CRITICAL ERROR', 'A file with the same name like the planned working directory ('.self::$workingDir.') already exists in the script folder. Please delete this file first and try to re-run this script.', 'error', true);
				exit;
			}
			else if ( !file_exists($this->workingDirPath) )
			{
				// Create the working directory, because it's not there yet
				@mkdir($this->workingDirPath);
				if ( !file_exists($this->workingDirPath) )
				{
					// are we able to create folders?
					$this->show_message('CRITICAL ERROR', 'The working directory ('.self::$workingDir.') could not be created. Maybe the PHP command mkdir is blocked on your server.', 'error', true);
					exit;
				}
			}
		}
		else
		{
			$this->show_message('CRITICAL ERROR', 'The working directory ('.self::$workingDir.') could not be created. Maybe there are no write permissions on the top folder where this script is executed in.', 'error', true);
			exit;
		}
	}


	/**
	 * Output a simple HTML page header with some basic styles to make a foundation for all outputs this script
	 * generates.
	 *
	 * @access public
	 */
	public function show_header()
	{
		if($this->headerShown)
		{
			return;
		}

		$this->headerShown = true;

		print '<!doctype html>';
		print '<html lang="en" dir="ltr">';
		print '<head>';
		print '	<meta charset="utf-8" />';
		print '	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />';
		print '	<meta name="author" content="CBACK Software" />';
		print '	<meta name="robots" content="noindex,nofollow" />';
		print '	<title>CBACK AppLoader</title>';
		print '	<style>';
		print '	* { margin: 0; padding: 0; }';
		print '	body { font: 16px Helvetica,Verdana,sans-serif; background: #ededed; color: #131313; }';
		print '	a, a:link, a:visited, a:active { color: #0063e3; text-decoration: none; transition: color linear 0.3s; }';
		print '	a:hover { color: #e34d00; }';
		print '	.cal-container { display: block; margin: 40px auto; max-width: 800px; padding: 40px; border: 2px #97a0ac solid; background: #f7f7f7; box-shadow: 1px 1px 24px rgba(0,0,0,0.3); }';
		print '	.cal-dlg { font-size: 0.8em; width: 90%; padding: 10px; margin: 20px auto; margin-bottom: 40px; display: block; border-radius: 4px; display: block; }';
		print '	.cal-warning { background: #f6e693; color: #272000; border: 1px #cdb200 solid; }';
		print '	.cal-error { background: #f59393; color: #270000; border: 1px #cc0000 solid; }';
		print '	.cal-success { background: #93f59e; color: #002705; border: 1px #00cc19 solid; }';
		print '	.cal-packrow { display: block; margin: 10px auto; padding: 10px; border: 1px #838383 solid; background: #e7e7e7; }';
		print '	.cal-packrow b { display: block; font-size: 1.4em; font-weight: 400; color: #000000; }';
		print '	.cal-packrow i { padding-left: 10px; color: #8f8f8f; }';
		print '	.cal-button { font-size: 1em; margin: 6px; float: right; text-align: center; width: 140px; display: block; padding: 11px 8px 8px 8px; border-radius: 8px; text-transform: uppercase; }';
		print '	.cal-install { background: #a7c0dd; border: 2px #34588a solid; color: #1a2634 !important; }';
		print '	.cal-install:hover { background: #b6cee9; border: 2px #244778 solid; }';
		print '	.cal-open { background: #a3e1bf; border: 2px #348a54 solid; color: #1a341a !important; }';
		print '	.cal-open:hover { background: #b1eecd; border: 2px #207640 solid; }';
		print '	.cal-spacer { height: 30px; display: block; }';
		print '	.cal-clear { clear: both; }';
		print '	footer { color: #6b6b6b; display: block; text-align: center; font-size: 0.7em; margin-bottom: 100px; }';
		print '	footer a, footer a:link, footer a:active, footer a:visited { color: #414141; }';
		print '	input[type=text], input[type=password], button { font-size: 1em; padding: 14px; border: 1px #919191 solid; margin-bottom: 16px; margin-top: 6px; }';
		print '	button { background: #969696; text-transform: uppercase; }';
		print '	button:hover { background: #b4b4b4; }';
		print '	</style>';
		print '</head>';
		print '<body>';
		print '	<div class="cal-container">';
	}


	/**
	 * Output a simple HTML page footer to make the end of the output generated by this script valid.
	 *
	 * @access public
	 */
	public function show_footer()
	{
		if(!$this->headerShown)
		{
			// no header, no footer. :)
			return;
		}

		print '</div>';
		print '	<footer>';
		print '		&copy;' . date('Y') . ' <a href="https://cback.net" target="_blank"><b>CBACK Software</b></a> &mdash; AppLoader is licensed under the MIT License.';
		print '	</footer>';
		print '<script> function cal_btn_click(elm){ elm.innerHTML=\'please wait ...\'; elm.style.opacity=0.5; } </script>';
		print '</body>';
		print '</html>';
	}


	/**
	 * Create and display a message box with warning, success or error messages.
	 *
	 * @access public
	 *
	 * @param string	$title			Title of the message box
	 * @param string	$text			Text of the message box
	 * @param string	$type			Type for the message box - valid values are 'error', 'warning' and 'success'
	 * @param bool		$terminate		Set this to true if you want to terminate the script after the message box is shown
	 */
	public function show_message($title = '', $text = '', $type = 'success', $terminate = false)
	{
		if(!$this->headerShown)
		{
			$this->show_header();
		}

		$boxClass = 'cal-success';
		switch($type)
		{
			case 'warning':
				$boxClass = 'cal-warning';
				break;

			case 'error':
				$boxClass = 'cal-error';
				break;
		}

		print '<div class="cal-dlg ' . $boxClass .'"><b>'. (string)$title .'</b><br /><br />' . (string)$text . '</div>';


		if($terminate)
		{
			// show the footer and terminate the script
			$this->show_footer();
			exit;
		}
	}


	/**
	 * This function is just a little 'translator' for $_GET and $_POST vars we need. This function checks, if the vars are
	 * existent to prevent php notices in strict mode and save a bit of code later.
	 *
	 * @access public
	 *
	 * @param string 	$varname		name of the var
	 * @param string	$location		GET or POST
	 * @param bool		$setcheck		set to true if you only want to know if the var is set or not. Same as "isset" then.
	 *
	 * @return mixed
	 */
	public function get($varname, $location = 'GET', $setcheck = false)
	{
		$location = strtoupper($location); // we don't need mb_ for our use here

		switch($location)
		{
			case 'GET':
				return (($setcheck)? isset($_GET[$varname]) : ((isset($_GET[$varname]))? $_GET[$varname] : '' ));

			case 'POST':
				return (($setcheck)? isset($_POST[$varname]) : ((isset($_POST[$varname]))? $_POST[$varname] : '' ));

			default:
				return false;
		}
	}


	/**
	 * Function to clean possibly malicious chars from a path string
	 *
	 * @access protected
	 *
	 * @param string	$in		pathname
	 * @return string			cleaned pathname
	 */
	protected function clean_path($in)
	{
		$in = (string)$in;														// must be a string
		$in = str_replace(array('..', '/', '\\', '%', '"', "'"), '', $in);		// remove bad chars
		$in = str_replace(' ', '_', $in);										// convert spaces to _

		return $in;
	}


	/**
	 * The startpage / mainpage of the script which lists available packages and shows the welcome screen
	 *
	 * @access public
	 */
	public function startpage()
	{
		$this->show_header();

		// check if the script is protected from public access
		if( !file_exists($this->path.'/.htaccess') && !file_exists($this->path.'/.htpasswd') )
		{
			$this->show_message('WARNING: SCRIPT FOLDER IS UNPROTECTED', 'It seems that everybody could access the folder with this script inside. We recommend to secure this folder from public access. On most webspaces you can do this for example with the integrated <a href="index.php?mode=hta">.htpasswd generator</a> of this script. If you don\'t want to secure the folder of this script we recommend to delete this file from your server after you are done using this script.', 'warning');
		}

		// Success message?
		if ( $this->get('msg', 'GET', true) )
		{
			$msgCode = trim(htmlspecialchars($this->get('msg', 'GET')));
			switch ( $msgCode )
			{
				case 'success':
					$this->show_message('PACKAGE INSTALLED SUCCESSFULLY', 'Your package was installed successfully. You should now be able to execute it by clicking on the &quot;OPEN&quot; button in the App list.', 'success');
					break;

				case 'htcreated':
					$this->show_message('.htaccess / .htpasswd GENERATED', 'Your .htaccess / .htpasswd folder protection was generated successfully. But you should test if it works properly on your server. If you get any problems just delete these two files with your FTP program again from the AppLoader folder.', 'success');
					break;
			}
		}

		// welcome screen
		print '<h1>CBACK AppLoader</h1>If you want to download and install one of the WebTools just click the &quot;Install&quot; button and wait a little moment until the package is installed.<div class="cal-spacer"></div>';

		// list installable packages
		foreach($this->appDownloads as $appInfo)
		{
			// Path Protection
			$appInfo[1] = $this->clean_path($appInfo[1]);

			// Button Display Handling (tool already installed or not?)
			$buttonLink		= 'index.php?mode=install&amp;packkey=' . $appInfo[1];
			$buttonText		= 'Install';
			$buttonExtra	= 'onclick="cal_btn_click(this);';
			$buttonClass	= 'cal-install';

			if(file_exists($this->workingDirPath.'/'.$appInfo[1]))
			{
				$buttonLink		= self::$workingDir . '/' . $appInfo[1] . '/';
				$buttonText		= 'Open';
				$buttonExtra	= 'target="_blank"';
				$buttonClass	= 'cal-open';
			}

			// Output List Element
			print '<div class="cal-packrow">';
			print '	<a href="' . $buttonLink . '" class="cal-button ' . $buttonClass . '" ' . $buttonExtra .'">' . $buttonText . '</a>';
			print '	<b>' . $appInfo[0] . '</b>';
			print '	<i>' . $appInfo[2] . '</i>';
			print '	<div class="cal-clear"></div>';
			print '</div>';
		}

		$this->show_footer();
	}


	/**
	 * Downloads and extracts a webapp package
	 *
	 * @access public
	 */
	public function package_installer()
	{
		$packkey = trim(htmlspecialchars($this->get('packkey', 'GET')));
		$workAry = array();

		foreach($this->appDownloads as $appInfo)
		{
			if($appInfo[1] == $packkey)
			{
				$workAry = $appInfo;
				break;
			}
		}

		if( count($workAry) <= 0 || !isset($workAry[1]) )
		{
			$this->show_message('ERROR', 'The package key was not found in the package list. Could not find a package with that name.', 'error', true);
			exit;
		}

		$workAry[1] = $this->clean_path($workAry[1]);

		if( file_exists($this->workingDirPath.'/'.$workAry[1]) )
		{
			$this->show_message('WARNING', 'It seems that the package was already downloaded and installed.', 'warning', true);
			exit;
		}

		// Download the file
		$fileStream  = fopen($this->workingDirPath.'/'.$workAry[1].'.zip', 'w+');
		$curlSession = curl_init($workAry[2]);
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($curlSession, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curlSession, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 AppLoader');
		curl_setopt($curlSession, CURLOPT_FILE, $fileStream);
		curl_exec($curlSession);
		curl_close($curlSession);
		fclose($fileStream);

		// Basic validation if the download worked
		if( !file_exists($this->workingDirPath.'/'.$workAry[1].'.zip') )
		{
			$this->show_message('WARNING', 'The download file could not be written to the file system.', 'warning', true);
			exit;
		}

		if( file_exists($this->workingDirPath.'/'.$workAry[1].'.zip') && filesize($this->workingDirPath.'/'.$workAry[1].'.zip') <= 0 )
		{
			@unlink($this->workingDirPath.'/'.$workAry[1].'.zip');
			$this->show_message('WARNING', 'The package download has failed. The downloaded file seems to be corrupted.', 'warning', true);
			exit;
		}

		// Extract the package
		$ZipExtract = new ZipArchive;
		$ZipSession = $ZipExtract->open($this->workingDirPath.'/'.$workAry[1].'.zip');
		$destfolder = $ZipExtract->getNameIndex(0);
		$destfolder = $this->clean_path($destfolder);
		if ($ZipExtract)
		{
			$ZipExtract->extractTo($this->workingDirPath.'/');
			$ZipExtract->close();
			@rename($this->workingDirPath.'/'.$destfolder.'/', $this->workingDirPath.'/'.$workAry[1].'/');
			@unlink($this->workingDirPath.'/'.$workAry[1].'.zip');
		}
		else
		{
			@unlink($this->workingDirPath.'/'.$workAry[1].'.zip');
			$this->show_message('WARNING', 'Extraction of the ZIP Package failed. Maybe the ZIP file is corrupted.', 'warning', true);
			exit;
		}

		// Back to the home screen
		header('Location: index.php?msg=success');
		exit;
	}


	/**
	 * Creates a .htaccess / .htpasswd protection for the script folder
	 *
	 * @access public
	 */
	public function htaccess_generator()
	{
		$sub = trim(htmlspecialchars($this->get('sub', 'GET')));

		// Try to delete .htaccess / .htpasswd file
		if ( $sub == 'del' )
		{
			if ( file_exists($this->path.'/.htaccess') ) { @unlink($this->path.'/.htaccess'); }
			if ( file_exists($this->path.'/.htpasswd') ) { @unlink($this->path.'/.htpasswd'); }
			header('Location: index.php?mode=hta');
			exit;
		}

		// Already existing .htaccess or .htpasswd?
		if( file_exists($this->path.'/.htaccess') || file_exists($this->path.'/.htpasswd') )
		{
			$this->show_message('.htaccess already exists', 'It seems there is already a .htaccess / .htpasswd file in this script folder. Do you want to delete these files? If so please click <a href="index.php?mode=hta&amp;sub=del">HERE</a> to start over. <b>ATTENTION</b> if you never generated a .htaccess/.htpasswd with this script it could be that there are important other things in the existing .htaccess file generated by your hosting environment. If this is the case you should <b>NOT</b> delete these files! Please be careful.', 'warning', true);
			exit;
		}

		// Generate the files
		if( $this->get('send', 'POST', true) )
		{
			$username = trim($this->get('username', 'POST'));
			$password = trim($this->get('password', 'POST'));

			$password = crypt($password, base64_encode($password));

			$htpass_content = "{$username}:{$password}";
			$htacc_content	= "AuthType Basic\nAuthName \"please login first\"\nAuthUserFile ".$this->path."/.htpasswd\nrequire valid-user";

			file_put_contents($this->path.'/.htpasswd', $htpass_content);
			file_put_contents($this->path.'/.htaccess', $htacc_content);

			header('Location: index.php?msg=htcreated');
			exit;
		}

		$this->show_header();
		print('<h1>Create .htaccess folder protection</h1>');
		print('Here you can create a .htaccess folder protection for this script. This should work on most servers, but please make sure the login really works properly after the protection is created. If you get a server error or error 500 after the script generated the files or you can\'t login, just delete the .htaccess and .htapasswd file with your FTP program from this folder and try to secure this script with other tools, for example the folder protection from your webhoster interface.');
		print('<div class="cal-spacer"></div>');
		print('<form action="index.php?mode=hta" method="post">');
		print('<input type="text" name="username" placeholder="username" value="" /> <input type="password" name="password" value="" placeholder="password" /> <button type="submit" name="send">Generate Folder Protection</button>');
		print('</form>');
		$this->show_footer();
	}

} // end of class CBACKAppLoader



/****************************************************************************
 *
 * Here's the rest of the script. Clean init, Class init, Function router
 *
 ****************************************************************************/

//
// Clean Init
// 1. Error Reporting
// 2. Locale & UTF8 ... to prevent warnings or problems with the content encoding in the browser
// 3. Set Timezone ... again just to prevent strict warnings, we don't really need timezone stuff in this script
//
$genErrLevel = -1;
if ( !defined('E_DEPRECATED') ) { define('E_DEPRECATED', 8192); }
$genErrLevel = E_ALL & ~E_NOTICE & ~E_DEPRECATED;
if ( version_compare(PHP_VERSION, '5.4.0-dev', '>=') )
{
	if ( !defined('E_STRICT') ) { define('E_STRICT', 2048); }
	$genErrLevel &= ~E_STRICT;
}
error_reporting($genErrLevel);

@setlocale(LC_CTYPE, 'de_DE.utf8', 'de_DE.UTF-8', 'en_US.utf8', 'en_US.UTF-8', 'en_GB.utf8', 'en_GB.UTF-8', 'de_AT.utf8', 'de_AT.UTF-8', 'de_CH.utf8', 'de_CH.UTF-8');
if( function_exists('mb_internal_encoding') )
{
	mb_internal_encoding('utf-8');
}
header('Content-type: text/html; charset=utf-8');

if( function_exists('date_default_timezone_set')&&function_exists('date_default_timezone_get') )
{
	date_default_timezone_set(@date_default_timezone_get());
}


//
// Initialize the Main Class of this Script
//
$CBACKAppLoader = new CBACKAppLoader();


//
// Route all functions
//
$mode = $CBACKAppLoader->get('mode', 'GET');
switch($mode)
{
	case 'install':
		$CBACKAppLoader->package_installer();
		break;

	case 'hta':
		$CBACKAppLoader->htaccess_generator();
		break;

	default:
		$CBACKAppLoader->startpage();
		break;
}