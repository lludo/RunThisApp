<?php
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