<?php 

/*
 *    RunThisApp allows sharing test builds of iOS apps with testers.
 *    Copyright (C) 2011 Ludovic Landry & Pascal Cans
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
@date_default_timezone_set(@date_default_timezone_get());


class logfile{
	function write($the_string)
	{
		if( $fh = @fopen( '/logfile.txt', 'a+' ) )
		{
			$datetime = new DateTime(); 
			$the_string = $datetime->format('Y/m/d H:i:s').': '.$the_string;
			fputs( $fh, $the_string, strlen($the_string) );
			fclose( $fh );
			return( true );
		}
			else
		{
			return( false );
		}
	}
	
	function writeln($s) {
		$this->write($s.PHP_EOL);
	}
}

?>