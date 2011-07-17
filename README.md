README
======

What is RunThisApp?
-------------------

RunThisApp is a website which provide to developer set of tools to distribute iOS applications Over-The-Air for your testers.

When the website is configure on a server, difference developers can work on the same platform and can:

- Add testers just by giving their name and email
- Upload an application that you want to test
- Send invite to testers to make them register their devices and install the app

When you send an invite to a tester, the system retrieve the tester device UDID, resign you app with the tester informations and sent back a new mail with download application info to the tester.

Run This App is a web project written in PHP ([RunThisApp website][1]).

Requirements
------------

RunThisApp is only supported on PHP 5.3.0 and up.

Installation
------------

1. The best way to install RunThisApp is to download it from [our Github repository][2].

2. Then you have to configure git submodules by executing the following commands in your git directory:
	
`	git submodule init
	git submodule update`
	
3. RunThisApp use [Doctrine][3] (an Object relational mapper for PHP) and an SQLite database. To initialize this database, in the core subfolder, you have to execute:
	
`	./doctrine orm:schema-tool:create`
	
4. For sending emails to testers and accessing your Apple Developer account, you have to fill the file credential.php:
	
`	<?php
		$CRED_USR = '__your_user__';
		$CRED_PWD = '__your_passwd__';
		
		$CRED_SMTP = 'smtp.gmail.com';		// example
		$CRED_SMTP_PORT = 465;				// example
		$CRED_SMTP_USR = '__your_email__';
		$CRED_SMTP_PWD = '__your_passwd__';
	?>`
	
5. Last step is to set the permissions to allow the server to write some folder. The following folders needs to be writable (chmod ugo+rwx __file__):

For the SQLitedatabase and Doctrine:

- /core/
- /core/database.sqlite
- /core/Proxies/

For app upload:

- /app/

NOTE: For application uploading, you may have to change the max upload file size in php.ini (the key is named "upload_max_filesize")

KNOWN ISSUES: The folder containing your website can't be named "back"

Documentation
-------------

The "[Quick Tour][2]" tutorial gives you a first feeling of the framework. If,
like us, you think that RunThisApp can help speed up your development and take
the quality of your work to the next level, read the official
[RunThisApp documentation][1].

[1]: http://www.runthisapp.com/
[2]: http://lludo.github.com/RunThisApp/
[3]: http://www.doctrine-project.org/projects/orm
