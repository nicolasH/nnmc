## NNMC

This is the software that drives [niconomicon.net](http://niconomicon.net). It is entirely [markdown](http://daringfireball.net/projects/markdown/) text files based.

It currently supports a content directory with subfolders for a blog, some notes and some projects.

- The blog posts should be in a 'blog' directory. The dates are in the filename, with YYYYMMDD.HHmm.url.in.address.text . Their url will be /blog/YYYY/MM/DD/hhmm.url.inaddress/ . By default the last 5 posts will be displayed, with a link to the 5 previous ones and a link to the whole list of posts at the bottom.

- The 'notes' are stored in an eponymous foder, and will be displayed hierarchically, with folders and their content's title on top, then that level's notes in alphabetical order.

- The 'projects' will be displayed hierarchically, with folders and their content's title on top. A .blurb file can be put in a project's directory, in which case its content will appear at the same level as the project's title.

- The 'pages' directory contains the content of the home page and the colophon.

You can adjust global settings in the 'base.php' and local or production settings in local / prod _config.php files.

The css design is currently inspired by [http://avandamiri.com/](http://avandamiri.com/) .

It uses php markdown extra by [Michel Fortin](http://michelf.com/)

Author : Nicolas HOIBIAN

This software is Licensed under the GPL v2.