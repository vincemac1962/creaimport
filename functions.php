<?php

function downloadPhotos($listingID)
{
	global $RETS, $RETS_PhotoSize, $debugMode;
	
	if(!$downloadPhotos)
	{
		if($debugMode) error_log("Not Downloading Photos");
		return;
	}

	$photos = $RETS->GetObject("Property", $RETS_PhotoSize, $listingID, '*');
	
	if(!is_array($photos))
	{
		if($debugMode) error_log("Cannot Locate Photos");
		return;
	}

	if(count($photos) > 0)
	{
		$count = 0;
		foreach($photos as $photo)
		{
			if(
				(!isset($photo['Content-ID']) || !isset($photo['Object-ID']))
				||
				(is_null($photo['Content-ID']) || is_null($photo['Object-ID']))
				||
				($photo['Content-ID'] == 'null' || $photo['Object-ID'] == 'null')
			)
			{
				continue;
			}
			
			$listing = $photo['Content-ID'];
			$number = $photo['Object-ID'];
			$destination = $listingID."_".$number.".jpg";
			$photoData = $photo['Data'];
			
			/* @TODO SAVE THIS PHOTO TO YOUR PHOTOS FOLDER
			 * Easiest option:
			 * 	file_put_contents($destination, $photoData);
			 * 	http://php.net/function.file-put-contents
			 */
			 
			$count++;
		}
		
		if($debugMode)
			error_log("Downloaded ".$count." Images For '".$listingID."'");
	}
	elseif($debugMode)
		error_log("No Images For '".$listingID."'");
	
	// For good measure.
	if(isset($photos)) $photos = null;
	if(isset($photo)) $photo = null;
}
