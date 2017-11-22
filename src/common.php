<?php
/*************************************************
 * @param int $lenght
 * @return string
 * @throws Exception
 *
 * Gets a real unique ID
 *
 */
function uniqidReal($lenght = 13) {
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}


/*************************
 * grabs the users IP address regardless from where they are coming from!
 * return $ip from remote_addr or http_x_forwarded
 */
function get_user_ip()
{
    if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) )//check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )//to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else //standard remote ip address
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


function get_user_host()
{
    if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
    {
        $host = @gethostbyaddr( $_SERVER['HTTP_X_FORWARDED_FOR'] );
    }
    else
    {
        $host = @gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
    }
    return $host;
}

/****************************************
 * @param bool $datetime
 * @param bool $unix_format
 * @param string $format
 * @return false|string
 *
 * date time function to return date how you like
 */
function datetimeformat($datetime = false, $unix_format = false, $format = 'Y-m-d H:i:s')
{
    $date = ( $datetime ? $datetime : date($format) );
    $time = ( $unix_format ? $date : strtotime($date) );
    return date($format, $time);
}


function stripslashes_deep($value)
{
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
}


function getFileSize($bytes)
{
    return ByteSize::formatMetric($bytes);
}

function getqrcodebase64($pastie_id)
{
    if( $pastie_id && !empty($pastie_id) ) {
        $image_data = file_get_contents('http://chart.apis.google.com/chart?cht=qr&chs=125x125&chl=http://pastiebin.com/' . $pastie_id . '&chld=H|0');
        return base64_encode($image_data);
    }
}

/**
 * Grab your Token: Go to https://api.slack.com/web to create your access-token. The token will look somewhat like this:
 * xoxo-2100000415-0000000000-0000000000-ab1ab1
 *
 * @param string $message The message to post into a channel.
 * @param string $channel The name of the channel prefixed with #, example #foobar
 * @return boolean
 */
function slack($message, $channel)
{
    $ch = curl_init("https://slack.com/api/chat.postMessage");
    $data = http_build_query([
        "token" => "SLACK TOKEN GOES HERE",
        "channel" => $channel, //"#mychannel",
        "text" => $message, //"Hello, Foo-Bar channel message.",
        "username" => "AppDevBot",
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
