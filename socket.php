<?php
error_reporting(~E_NOTICE);
set_time_limit (0);
 
$address = "127.0.0.1";
$port = 5003;
$max_clients = 10;
 
if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
 
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
 
echo "Socket created \n";
 
// Bind the source address
if( !socket_bind($sock, $address , $port) )
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
 
    die("Could not bind socket : [$errorcode] $errormsg \n");
}
 
echo "Socket bind OK \n";
 
if(!socket_listen ($sock , 10))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
 
    die("Could not listen on socket : [$errorcode] $errormsg \n");
}
 
echo "Socket listen OK \n";

socket_set_nonblock($sock);

echo "Waiting for incoming connections... \n";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "u571537793_pf0EG";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//array of client sockets
$client_socks = array();
$flag_handshakes = array();
$clients = array();

//array of sockets to read
$read = array();
 
//start loop to listen for incoming connections and process existing connections
while (true)
{
    //prepare array of readable client sockets
    $read = array();
 
    //first socket is the master socket
    $read[0] = $sock;
 
    //now add the existing client sockets
    for ($i = 0; $i < $max_clients; $i++)
    {
        if($client_socks[$i] != null)
        {
            $read[$i+1] = $client_socks[$i];
        }
    }
 
    for ($i = 0; $i < $max_clients; $i++)
    {
        if (in_array($client_socks[$i] , $read))
        {
            if ($flag_handshakes[$i] == false) continue;
            if ($clients[$i] == null) continue;
            
            $response = get_urgent_lessons($conn, $clients[$i]);
            socket_write($client_socks[$i], encode($response));
            print("> ".$response."\n");
        }
    }

    echo "Sending output to client \n";

    //now call select - blocking call
    echo "socket_select\n";
    if(socket_select($read , $write , $except=null , 5.0) < 1)
    {
        continue;

        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
 
        die("Could not listen on socket : [$errorcode] $errormsg \n");
    }
 
    //if ready contains the master socket, then a new connection has come in
    if (in_array($sock, $read))
    {
        for ($i = 0; $i < $max_clients; $i++)
        {
            if ($client_socks[$i] == null)
            {
                echo "socket_accpet\n";
                if(($newclientsock = socket_accept($sock)) === false) continue;
            
                echo "Client $newclientsock has connected\n";
                $client_socks[$i] = $newclientsock;
                
                //display information about the client who is connected
                if(socket_getpeername($client_socks[$i], $address, $port))
                {
                    echo "Client $address : $port is now connected to us. \n";
                }
                
                $bytes = @socket_recv($client_socks[$i], $data, 2048, 0);
                if ($flag_handshakes[$i] == false) {
                    if ((int)$bytes == 0) continue;
                    
                    //print("Handshaking headers from client: ".$data."\n");
                    
                    if (handshake($client_socks[$i], $data, $sock)) {
                        $flag_handshakes[$i] = true;
                    }
                }
                // elseif ($flag_handshakes[$i] == true) {
                //     if ($data != "") {
                //         $decoded_data = unmask($data);
                //         print("< ".$decoded_data."\n");
                //         $response = strrev($decoded_data);
                //         socket_write($client_socks[$i], encode($response));
                //         print("> ".$response."\n");
                //         // socket_close($client_socks[$i]);
                //         // unset($client_socks[$i]);
                //         // $flag_handshake = false;
                //     }
                // }
            }
        }
    }
 
    //check each client if they send any data
    for ($i = 0; $i < $max_clients; $i++)
    {
        if (in_array($client_socks[$i] , $read))
        {
            if ($flag_handshakes[$i] == false) continue;
            
            $input = socket_read($client_socks[$i] , 1024);
 
            if ($input == null)
            {                
                //zero length string meaning disconnected, remove and close the socket
                echo "Client {$client_socks[$i]} has disconnected\n";

                socket_close($client_socks[$i]);
                unset($client_socks[$i]);
                $flag_handshakes[$i] = false;

                continue;
            }
 
            $decoded_data = unmask($input);
            print("< ".$decoded_data."\n");
            $clients[$i] = explode(":", $decoded_data);
        }
    }
}

mysqli_close($conn);
socket_close($sock);

function handshake($client, $headers, $socket) {

    if (preg_match("/Sec-WebSocket-Version: (.*)\r\n/", $headers, $match))
        $version = $match[1];
    else {
        print("The client doesn't support WebSocket");
        return false;
    }

    if ($version == 13) {
        // Extract header variables
        if (preg_match("/GET (.*) HTTP/", $headers, $match))
            $root = $match[1];
        if (preg_match("/Host: (.*)\r\n/", $headers, $match))
            $host = $match[1];
        if (preg_match("/Origin: (.*)\r\n/", $headers, $match))
            $origin = $match[1];
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $match))
            $key = $match[1];

        $acceptKey = $key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
        $acceptKey = base64_encode(sha1($acceptKey, true));

        $upgrade = "HTTP/1.1 101 Switching Protocols\r\n".
            "Upgrade: websocket\r\n".
            "Connection: Upgrade\r\n".
            "Sec-WebSocket-Accept: $acceptKey".
            "\r\n\r\n";

        socket_write($client, $upgrade);
        return true;
    } else {
        print("WebSocket version 13 required (the client supports version {$version})");
        return false;
    }
}

function unmask($payload) {
    $length = ord($payload[1]) & 127;

    if ($length == 126) {
        $masks = substr($payload, 4, 4);
        $data = substr($payload, 8);
    }
    elseif($length == 127) {
        $masks = substr($payload, 10, 4);
        $data = substr($payload, 14);
    }
    else {
        $masks = substr($payload, 2, 4);
        $data = substr($payload, 6);
    }

    $text = '';
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}

function encode($text) {
    // 0x1 text frame (FIN + opcode)
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if ($length <= 125)
        $header = pack('CC', $b1, $length);
    elseif($length > 125 && $length < 65536)$header = pack('CCS', $b1, 126, $length);
    elseif($length >= 65536)
    $header = pack('CCN', $b1, 127, $length);

    return $header.$text;
}

function get_urgent_lessons($conn, $client) {

    $sql = "SELECT COUNT(*) AS cnt FROM lesson_progress WHERE TIMESTAMPDIFF(MINUTE,lesson_progress.start_time,CURRENT_TIMESTAMP)<=60 AND TIMESTAMPDIFF(MINUTE,lesson_progress.start_time,CURRENT_TIMESTAMP)>0 AND lesson_progress.{$client[0]}_id={$client[1]}";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            return $row["cnt"];
        }
    } else {
        echo "0 results";
    }
    return 0;
}