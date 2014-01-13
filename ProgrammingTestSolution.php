<?php

$UseCustomHeader = 1;   // setting to indicate using custom header (1 or 0) rather than REMOTE_ADDR header


function ShowFormattedField ( $FieldLabel, $FieldValue ) {   // display data field on page (input field label & field value)
        if ( strlen($FieldValue) > 0 ) {   // only show if has value
                echo "<b>" . $FieldLabel . "</b>: " . $FieldValue . "<br/>\n";
        }
}

class HTTPRequestMessage {

        private $IPAddress;
        private $HostName;

        function ShowAllHeaders() {   // show all HTTP headers - for debugging
                foreach ( $_SERVER as $SERVERVariableName => $SERVERVariableValue ) {   // iterate through SERVER variables
                        if ( substr($SERVERVariableName,0,5) == "HTTP_" ) {   // only show if begins with HTTP_
                                ShowFormattedField( $SERVERVariableName, $SERVERVariableValue );   // show name and value
                        }
                }
        }

        function GetClientIPAddress() {   // get client's IP address from HTTP header (output ip address)
                $this->IPAddress = "";
                global $UseCustomHeader;
                if ($UseCustomHeader == 0) {   // if using custom header
                        $this->IPAddress = $_SERVER['REMOTE_ADDR'];   // get value from SERVER variable
                }
                else if ($UseCustomHeader == 1) {   // if not using custom header
                        $this->IPAddress = $_SERVER['HTTP_CUSTOM_IP_ADDRESS'];   // get value from custom HTTP header
                }

                return $this->IPAddress;
        }

        function ShowClientIPAddress($IPAddress) {   // show client's ip address on page (input ip address)
                ShowFormattedField( "Client IP Address", $IPAddress );
        }

        function GetClientHostName($IPAddress) {   // derive client's host name based on DNS lookup of ip (input ip, output host)
                //$HostName = $_SERVER['REMOTE_HOST']; // for testing only
                $this->HostName = gethostbyaddr($IPAddress);
                return $this->HostName;
        }

        function ShowClientHostName($HostName) {   // show client's host name on pahe (input host name)
                ShowFormattedField( "Host Name", $HostName );
        }

}

class Network {
        private $Networks = array (
                ".comcast.net" => "Comcast",
                ".rr.com" => "Time Warner",
                ".myvzw.com" => "Verizon Wireless",
                ".verizon.net" => "Verizon"
        );

        /*
        // examples tested:
        $IPAddress = '98.200.227.100'; // comcast
        $IPAddress = '98.003.003.100'; // time warner
        $IPAddress = '97.003.003.100'; // verizon wireless
        $IPAddress = '108.035.125.100'; // verizon
        */

        private $NetworkDetails = array (
                "Comcast" => array(
                        "Size" => "Medium",
                        "Region" => "Asia"
                ),
                "Verizon Wireless" => array(
                        "Size" => "Small",
                        "Region" => "North America"
                ),
                "Time Warner" => array(
                        "Size" => "Large",
                        "Region" => "Africa"
                ),
                "Verizon" => array(
                        "Size" => "Small",
                        "Region" => "South America"
                )
        );

        function GetClientNetwork($HostName) {   // translate host name to network name (input host name, output network name)
                foreach ( $this->Networks as $NetworkSearchString => $NetworkName ) {   // iterate through networks
                        if ( stripos( $HostName, $NetworkSearchString ) !== FALSE ) {   // if host name contains search string
                                $FinalNetwork = $NetworkName;
                        }
                }
                return $FinalNetwork;
        }

        function ShowClientNetworkDetails($Network) {   // show details of client's network (input network name)
                ShowFormattedField ( "Network Size", $this->NetworkDetails[$Network]["Size"] );
                ShowFormattedField ( "Network Region", $this->NetworkDetails[$Network]["Region"] );
        }

        function ShowClientNetwork($Network) {   // show client's network carrier on page (input network)
                ShowFormattedField( "Network Carrier Name", $Network );
        }

}

// Create message object
$msg = new HTTPRequestMessage;

//$msg->ShowAllHeaders();   // for testing only

// Get IP address
$ip = $msg->GetClientIPAddress();

// Show IP address on page
$msg->ShowClientIPAddress($ip);

// Get host name from IP address
$hst = $msg->GetClientHostName($ip);

// Show host name on page
$msg->ShowClientHostName($hst);

// Create network object
$net = new Network;

// Translate host name to network name
$netname = $net->GetClientNetwork($hst);

// Show network name on page
$net->ShowClientNetwork($netname);

// Show network details
$net->ShowClientNetworkDetails($netname);


?>
