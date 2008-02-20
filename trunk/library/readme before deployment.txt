Welcome to your generated project.

You will have to make some mandatory configuration entries to make everything work:

1: If your site is not deployed in the root of domainname you will have to alter the 'rewritebase' property in the .htaccess
2: GET SOME SECURITY!  Set up some simple .htpasswd security or so, so that only you can access these pages. 
   This project gives you full control over your database contents and there's nothing stopping anybody from deleting all records.
   If you do not know how this works i recommend this site: http://shop.alterlinks.com/htpasswd/htpasswd.php
3: change settings/dbsettings.php to match your host/database/username/password.
4: chmod the logs directory to 777 that doesn't work to make it writable (only for linux)

5: you're done!
