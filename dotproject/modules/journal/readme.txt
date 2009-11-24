##
## journal module - a quick hack of the history module by HGS 3/16/2004

## (c) Copyright
## J. Christopher Pereira (kripper@imatronix.cl)
## IMATRONIX
##

The idea was to get all project related notes from telephone conversations and meetings off of paper and into the DB. A simple input screen was desired so people would actually use the darn thing. The existing history module required very little tweaking to accomplish the goal.

Installation:
Unzip to your DotProject directory
Go to System Admin->Modules->View Modules and click install. 
If all goes well 'Journal' should 'disabled' and 'hidden'. Enable both settings and shuffle the journals position as you see fit.

To create a 'Journal' tab for the projects page, add the line of code below to: your-dotproject-directory/modules/projects/view.php at or near line 207. 


//begin journal add

$tabBox->add( "{$AppUI->cfg['root_dir']}/modules/journal/index", 'Journal' );

//end journal add


You'll see other entires similar to the one you're about to add.
I'd suggest you place the new entry at the bottom of the existing list to avoid interfering with other project functions that may be dependent on the tab order.

Yet to finish:
	Standard DP date pickers - defaults to entire project or last XX days.
	Search bar - still kinda tweaky give me another week or so.

File List:

/modules/journal/readme.txt
/modules/journal/setup.php
/modules/journal/index.php
/modules/journal/addedit.php
/images/icons/notepad.gif