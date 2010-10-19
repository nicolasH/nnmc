NNMC

This is the software that drives niconomicon.net . It is entirely markdown text files based.

It currently supports a content directory with subfolders for a blog, some notes and some projects.

- The blog posts should be in a blog directory. The dates are in the filename, with YYYYMMDD.HHmm.url.in.address.text . Their url will be /blog/YYYY/MM/DD/hhmm.url.inaddress/ . By default the last 5 posts will be displayed, with a link to the 5 previous ones and a link to the whole list of posts at the bottom.

- The notes will be displayed hierarchically, with folers and their content's title on top.

- The project will be displayed hierarchically, with folders and their content's title on top. A .blurb file can be put in a project's directory, in which case its content will appear at the same level as the project's title.

The css design is currently inspired by http://avandamiri.com/ .

It uses php markdown extra by [Michel Fortin](http://michelf.com/)

Author : Nicolas HOIBIAN

Licensed under ...