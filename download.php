<?PHP

/* Script Variables */
// Lots of output, saves requests to a local file.
$debugMode = false; 
// Initially, you should set this to something like "-2 years". Once you have all day, change this to "-48 hours" or so to pull incremental data
$TimeBackPull = "-7 days";

/* RETS Variables */
require("PHRets_CREA.php");

/* Photo functions */
require("functions.php");

$RETS = new PHRets();
$RETSURL = "http://data.crea.ca/Login.svc/Login";
$RETSUsername = "";
$RETSPassword = "";
$RETS->Connect($RETSURL, $RETSUsername, $RETSPassword);
$RETS->AddHeader("RETS-Version", "RETS/1.7.2");
$RETS->AddHeader('Accept', '/');
$RETS->SetParam('compression_enabled', true);
$RETS_PhotoSize = "LargePhoto";
$RETS_LimitPerQuery = 100;
if($debugMode /* DEBUG OUTPUT */)
{
	//$RETS->SetParam("catch_last_response", true);
	$RETS->SetParam("debug_file", "C:\xampp\htdocs\rets\rets_debug.txt");
	$RETS->SetParam("debug_mode", true);
}



/* NOTES
 * With CREA, You have to ask the RETS server for a list of IDs.
 * Once you have these IDs, you can query for 100 listings at a time
 * Example Procedure:
 * 1. Get IDs (500 Returned)
 * 2. Get Listing Data (1-100)
 * 3. Get Listing Data (101-200)
 * 4. (etc)
 * 5. (etc)
 * 6. Get Listing Data (401-500)
 *
 * Each time you get Listing Data, you want to save this data and then download it's images...
 */
 
error_log("-----GETTING ALL ID's-----");
//$DBML = "(LastUpdated=" . date('Y-m-d', strtotime($TimeBackPull)) . ")";
$DBML = "(ID=*),(DestinationID=35763)";
//$DBML = "(ID=21002835)";
$params = array("Limit" => 1, "Format" => "STANDARD-XML", "Count" => 1);
$results = $RETS->SearchQuery("Property", "Property", $DBML, $params);
$totalAvailable = $results["Count"];
error_log("-----".$totalAvailable." Found-----");
if(empty($totalAvailable) || $totalAvailable == 0)
	error_log(print_r($RETS->GetLastServerResponse(), true));	
for($i = 0; $i < ceil($totalAvailable / $RETS_LimitPerQuery); $i++)
{
	$startOffset = $i*$RETS_LimitPerQuery;
	
	error_log("-----Get IDs For ".$startOffset." to ".($startOffset + $RETS_LimitPerQuery).". Mem: ".round(memory_get_usage()/(1024*1024), 1)."MB-----");
	$params = array("Limit" => $RETS_LimitPerQuery, "Format" => "STANDARD-XML", "Count" => 1, "Offset" => $startOffset);
	$results = $RETS->SearchQuery("Property", "Property", $DBML, $params);			
	foreach($results["Properties"] as $listing)
	{
		$listingID = $listing["@attributes"]["ID"];
		if($debugMode) error_log($listingID);
	
		/* @TODO Handle $listing array. Save to Database? */
		
		/* @TODO Uncomment this line to begin saving images. Refer to function at top of file */
		//downloadPhotos($listingID);
	}
}

$RETS->Disconnect();

/* This script, by default, will output something like this:

Connecting to RETS as '[YOUR RETS USERNAME]'...
-----GETTING ALL ID's-----
-----81069 Found-----
-----Get IDs For 0 to 100. Mem: 0.7MB-----
-----Get IDs For 100 to 200. Mem: 3.7MB-----
-----Get IDs For 200 to 300. Mem: 4.4MB-----
-----Get IDs For 300 to 400. Mem: 4.9MB-----
-----Get IDs For 400 to 500. Mem: 3.4MB-----
*/

?>
