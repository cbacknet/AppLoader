# CBACK AppLoader FAQ

######How can I add own Webtools to download?
Quite at the beginning of the _index.php_ file you can find a little configuration array which looks
like this:
```php
private $appDownloads = array(
	array('phpMyAdmin', 'pma', 'https://files.phpmyadmin.net/phpMyAdmin/4.7.8/phpMyAdmin-4.7.8-all-languages.zip'),
	array('MySQLDumper', 'msd', 'https://github.com/DSB/MySQLDumper/archive/master.zip')
);
```
 	
Just add the tools you need by extending the array. Format looks like that:
```php
array('MyAppNameToDisplay', 'MyFolderName', 'TheDownloadURL')
```

- **key 0** contains the Display Name of the App in the script. Enter whatever you want there.
- **key 1** contains the name of the folder the script will extract the ZIP into. Enter a desired folder name there without a path
- **key 2** has the download link to the ZIP file.

<br /><br />

######Which download URLs can I add?
You can add every download URL to a ZIP file. Other file types are currently not supported. If you want
to add a tool from **GitHub** a ZIP download should always be available.

<br /><br />

######Will you add tool ... to the script?
By default we only integrated MySQLDumper and phpMyAdmin. But you can add own tools you need very
easily (see first chapter of this FAQ). To keep this script as simple as possible and to reduce the
maintenance effort we will unfortunately not add more sources to the base script. But feel free to fork
this project and extend it however you want to! Maybe you can also create some predefined ZIP packages
on your own servers with tools you personally need and just use your own modified version of this tool
in the future? :) It's only just important that the ZIP files you add are extractable by the PHP
ZipArchive functionality, which has some restrictions. (For example with hidden files on Linux servers).

<br /><br />

######Why is there no simple input field for any URL to download something from?
Even though we write multiple warning messages that you should protect the folder with this tool in it or
even delete the tool again after you downloaded everything you needed we assume, that not everybody will
read those notes carefully. Or maybe someone just forgets to secure the folder by accident. The Web Tools
you can download here normally need a valid login to work, so even if somebody is able to download them
there is nothing extremely bad happening if an unauthorized person could ever get access to this tool.

However allowing you to download anything with a simple input field could end up in a huge security risk
for your webspace. That's why we think a tool with less features is in this case better.

Beside that this really is mentioned only as little time-saver and helper-script and should not turn into
a huge ControlPanel Software. There are other tools doing that but those are again more than one single file
to upload. :)

<br /><br />

######I can't delete the stuff I downloaded with my FTP Account!
Some hosters don't allow the FTP user to delete files or folders that were created by the PHP
Interpreter. Usually these hosters provide something like "fix file permissions" or "reassign file
owner on webspace" or something like that in the hoster's administration interface. For example
the hoster **All-Inkl** provides a function like that in the **KAS** interface. Just ask your hoster
for help if it happens and you don't know how to reassign the files to the FTP account.

<br /><br />

######I locked myself out / can't access the AppLoader folder anmyore
If a password proection generated with the integrated .htpasswd generator doesn't work or there is
any other case that locked you out from the AppLoader folder just delete the .htaccess and .htpasswd
files with your FTP program from the AppLoader folder. This removes any locks generated by this script. 

<br /><br />

######I get a timeout or memory_limit warning when using the tool
Normally most of the webspaces out there should have enough configured capacity to download and extract
one of the web tools provided. However it could happen that the PHP configuration on some webspaces is
so restricted that the script timeout or script memory limit setting prevents this script from handling
the files to download and extract. If you can't lift up these values it seems that this tool unfortunately
will not help you.

<br /><br />

######Is this tool using external resources to work?
Absolutely not! Everything this tool does is packed into one single PHP file. There are no connections
to external resources or CDNs (except of course when you download a tool which obviously needs to
connect to the download file). We thought about using an external WebFont/FontAwesome or something like
that to make the interface look nicer, but the goal was to keep this as simple as possible and with not
more load on your internet connection as really needed.

<br /><br />

######Why not just download the tools on the server? (Like over SSH etc.)
We know that there are still a lot of people and clients out there who just have little websites and
use shared webhosts for this with a limited possibility when it comes to access on the machine. Some
are so restricted that you basically only have an FTP connection available. This little helper is made
for situations like that. If you have a server or a vserver with direct access to everything there
surely are even faster ways available to download and install all the webtools you need.