<?php

/***********************
 * Middleware to check for valid request from IFTTT
 */
$app->add(function ($request, $response, $next) {
    $error_msgs = array();
    $IFTTTChannelKey = $request->getHeader('IFTTT-Channel-Key');
    if( isset( $IFTTTChannelKey[0] ) &&  $IFTTTChannelKey[0] == 'INVALID' )
    {
        $error_msgs[] = array('message' => 'Invalid IFTTT Channel Key');
        $error = array('errors' => $error_msgs);
        return $response->withStatus(401)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->write(json_encode($error));
    }
    $response = $next($request, $response);
    return $response;
});
