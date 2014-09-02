<?php
$headers = array(
    'HTTP_HOST' => 'Host',
    'HTTP_REFERER' => 'Referer',
    'HTTP_USER_AGENT' => 'User-Agent');
?>
<?php include('../../inc/header.html'); ?>
<h2>HTTP Requester</h2>
<form action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="post">
<h3>Request</h3>
<textarea class="http" rows="6" cols="50" name="req"><?php
    if(isset($_POST['req'])) {
        echo htmlspecialchars($_POST['req']);
    }
    else {
        echo 'GET '.$_SERVER['REQUEST_URI']." HTTP/1.1\n";
        foreach($headers as $key => $name) {
            if(isset($_SERVER[$key])) {
                echo $name.': '.$_SERVER[$key]."\n";
            }
        }
        echo "Connection: close\n";
    }
?>
</textarea><br/>
<input type="submit" value="Send"/>
<?php
    function spacify($str) {
        $str = str_replace("\t",'        ',$str);
        $str = str_replace(' ','&nbsp;',$str);
        return $str;
    }

if($_SERVER['SERVER_ADDR']=='127.0.0.1') {
    ?>
<!--
This request is coming from the server
itself, not your browser, so it will not
send yet another request. I can't have
any infinite loops.
-->
<?php
}
else if(isset($_POST['req'])) {
    $req = trim($_POST['req'])."\n\n";
    $sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
    socket_connect($sock,'127.0.0.1',80);
    socket_set_option($sock,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>2, "usec"=>0));
    socket_send($sock,$req,strlen($req),MSG_EOR);
    $resp = socket_read($sock,4096);
    socket_close($sock);
    echo "<h3>Response</h3>\n";
    echo '<textarea disabled="disabled" class="http" rows="10" cols="50">'.spacify(htmlspecialchars($resp)).'</textarea>';
}
?>
</form>
<?php include('../../inc/footer.html'); ?>
