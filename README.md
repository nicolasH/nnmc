## NNMC

This is the software that drives [niconomicon.net](http://niconomicon.net). It is entirely [markdown](http://daringfireball.net/projects/markdown/) text files based.

It currently supports a content directory with subfolders for a blog, some notes and some projects.

- The blog posts should be in a 'blog' directory. The dates are in the filename, with YYYYMMDD.HHmm.url.in.address.text . Their url will be /blog/YYYY/MM/DD/hhmm.url.inaddress/ . By default the last 5 posts will be displayed, with a link to the 5 previous ones and a link to the whole list of posts at the bottom.

- The 'notes' are stored in an eponymous foder, and will be displayed hierarchically, with folders and their content's title on top, then that level's notes in alphabetical order.

- The 'projects' will be displayed hierarchically, with folders and their content's title on top. A .blurb file can be put in a project's directory, in which case its content will appear at the same level as the project's title.

- The 'pages' directory contains the content of the home page and the colophon.

You can adjust global settings in the 'base.php' for options common between local and remote version, and local or production settings in local / prod _config.php files.

To deploy the script in an already existing directory :

	git init
	git remote add origin git://github.com/nicolasH/nnmc.git
	git fetch origin

The following command will actually make the files appear in the directory. I use the `-f` to overwrite the files that are both in the repository and on the website.

	git checkout -f -t origin/master -b master
	
Then just run the script that symlinks the .htaccess and config.php to your location-appropriate files:
	
	bash setup.sh prod
or 
	bash setup.sh local

To automate the deployment process, you can copy your ssh public key on your web host and add a remote repository to your config. With the appropriate `post-receive` hooks, pushing should also deploys the new version ( I added `git --work-tree /home/public/ checkout -f`).


The css design borrows from [http://avandamiri.com/](http://avandamiri.com/) and [http://neugierig.org/](http://neugierig.org/).

It uses php markdown extra by [Michel Fortin](http://michelf.com/)

Author : Nicolas HOIBIAN


This software is Licensed under the GPL v2.