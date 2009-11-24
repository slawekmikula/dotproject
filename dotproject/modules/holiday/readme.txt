dotProject Holiday Module 0.2
-----------------------------

INTRODUCTION
This module integrates the PEAR library for calculating holidays. It
also features a whitelist and a blacklist to help those who doesnt
want to write their own holiday drivers. The implementation is done
by Sensorlink AS, as we needed it to calculate workhours and project
management.

LEGAL
This module is still in early development, aside from the mediocre
layout it might have its quirks and could misbehave. Sensorlink AS
is not responsible for any damage it might cause, but please report
any bugs

CONTRIBUTING
If you decide to write a holiday driver, please contact the package
maintainer at PEAR and contribute it back to the Date_Holiday project

INSTALLATION
1. Decompress the tarball to the dotproject module directory like this:
	cd /path/to/dotproject
	cd modules
	tar xzvf /path/to/tarball/dpholiday.tar.gz
2. Install the module from the GUI as normal
3. Open the dateclass in the editor of choise:
	vi /path/to/dotproject/classes/date.class.php
4. Paste the following in the beginning of the isWorkingDay function (Line 136)
	// Holiday module, check the holiday database
	global $baseDir;
	require_once $baseDir."/modules/holiday/holiday_func.php";
	if(isHoliday($this))
	{
        	return 0;
	}
5. Save and exit


