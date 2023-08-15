# CBACK AppLoader
Do you know situations when you work somewhere with a very slow internet connection on a client
website with no direct access to the server itself via SSH, but you quickly need a tool like **phpMyAdmin**
or **MySQLDumper** for some database work? Downloading these tools manually and upload them over a slow
FTP/FTPS connection to the destination server can take a lot of time.

### Now CBACK AppLoader is here to be your time saver!

The **AppLoader** is just a little helper-script &mdash; written in PHP &mdash; which allows you to download
database tools or even other tools _(if you add them to the script)_ from the client webspace itself and
automatically extract them into a working-directory. This will save you the whole download/upload time, and
you can get back to your work much faster. For this script we really went for simplicity: Just create an
empty folder, give it write permission and upload the one single PHP file from this package. No fancy stuff
that will again cost you a lot of upload time in regions with slow connection speeds and no possibility to
download stuff directly on the remote machine/webspace.

<br /><br />

## So what does this tool do exactly?
Once uploaded you can access the **AppLoader** from your browser. The script will then create a
working-directory within its own folder where the tools will be downloaded and extracted into. By default,
you have the possibility to directly download and install **phpMyAdmin** or **MySQLDumper**. But you can
add own tools into the script code if necessary.

Once the tool downloaded and extracted the files you can access them in the working-directory and use them
as usual. **AppLoader** will detect that the tool is installed and will also provide you a handy link from
its overview page.

<br /><br />

## How to use this tool
* Upload the folder **AppLoader** with its included **index.php** file to the destination webspace

* Give the folder **AppLoader** write permissions (if you want you can also rename the folder)

* Open the **AppLoader** folder with the index.php in your browser like https://example.com/AppLoader/

* If your webspace supports the tool, it will create a working-directory within its own folder automatically and
you will see the options to download & install the DB tools with just one click _(like a little package manager
if you will)_

* **ATTENTION:** You should always secure the directory with **AppLoader** in it from public access and
give the folder a cryptical name in the first place. If you finished downloading the tools you need, you
can also delete the index.php file again from your webspace if you want.

<br /><br />

## I have a question
Check out our little [FAQ](https://github.com/cbacknet/AppLoader/blob/master/FAQ.md) here, maybe we
already got your question covered!

<br /><br />

## System Requirements
* PHP >= 5.6
* ZipArchive Support
* curl Support
* PHP memory_limit > 16M
* PHP timeout > 20sec (ZIP extraction could take a moment to process)

Normally most webspaces these days should fulfill these requirements. If not, chances are good that
you would also not be able to use the downloadable database tools either. ;)

<br /><br />

## Copyright and License
Copyright [CBACK&reg; Software](https://cback.net) under the [MIT License](https://github.com/cbacknet/AppLoader/blob/master/LICENSE).


Enjoy!