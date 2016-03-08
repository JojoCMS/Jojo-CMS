Google webmaster tools requires verification that you are in charge of the domain name. This is achieved by uploading an empty file onto your web server, with a filename specified by Google (eg google2fda74d09ef4529b.html).

This plugin makes this process a little easier. Instead of uploading a file via FTP, you can simply copy-paste the filename into the "Edit Options" part of Jojo CMS.

Our goal is to keep the root folder clean and tidy as much as possible, and this is another way of doing this.

Multiple valid filenames can be entered - simply add one per line.

This plugin returns a 200 response for a valid filename or a 404 for an invalid one.