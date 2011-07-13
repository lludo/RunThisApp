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
 
function retreivePlistFromAsn($ciphertext_file) {
	
	//Title: Simple PHP BER/DER/ASN.1 basic decoder
	//Author: Jean-Luc Cyr
	//Date: 2008-02-13
	//Desc: Simple BER Printable string dumper
	//	read BER data from a file
	
	// Set max number of tag to parse (0 = no limit)
	$limit = 0;
	 
	//Open data file
	$f = fopen($ciphertext_file,'rb');
	//Set read tag number to 0
	$c=0;
	//While not end of file
	while(!feof($f) && empty($plist)) {
		//echo "\r\n";
		//Read first block data type
		$type = ord(fread($f,1));
		if ($type==0)
		  break;
		//Read first len block
		$len = ord(fread($f,1));
		$le = 0;
		//If first bit of len is set (1)
		//we have a multiple bit len to read
		//echo "Read Len ".($len)." Bytes\r\n";
		if (($len & 128)==128) {
			//echo "Big Len ".($len & 127)." Bytes\r\n";
			for ($i = 0; $i < ($len & 127); $i++)
			  $le = $le * 256 + ord(fread($f,1));
			$len = $le;
		}
		//Findout data type (first 2 bits)
		$cl = ($type & (128+64) ) >> 6;
		switch($cl) {
		 case 0:
			$cla = 'Universal';
			break;
		 case 1:
			$cla = 'application';
			break;
		 case 2:
			$cla = 'context-specific';
			break;
		 case 3:
			$cla = 'private';
			break;
		}
		//Dump some info
		//echo sprintf("[$cla] Type: %x (%d), len: %d\r\n",$type,$type,$len);
		//ignore sequences.
		if ($type & (1 << 5)) {
			//echo "6th bit is on, it a structure, dive in!\r\n";
			continue;
		}
		//Read data chunk
		if ($len == 0) {
			$data = null;
		} else {
			$data = fread($f, $len);
		}
		//Display data chunk based on type
		switch($type) {
		 case 2: // integer
			break;
		 case 3: // bit string
			break;
		 case 4: // octet string
			//printf("Data: $data\r\n");
			$plist = $data;
			break;
		 case 5: // null
			break;
		 case 6: // object identifier
			break;
		 case 16: // sequence and sequence of
			break;
		 case 17: // set and set of
			break;
		 case 19: //string
			//printf("Data: $data\r\n");
			break;
		 case 20: // t61string
			break;
		 case 22: // ia5string
			break;
		 case 23: // utctime
			break;
		 default:
			break;
		}
		//increment count
		$c++;
		//check if we reach the number of tag specified
		if (($c>$limit)&&($limit>0)) break;
	}
	 
	//close our input file
	fclose($f);
	return $plist;
}