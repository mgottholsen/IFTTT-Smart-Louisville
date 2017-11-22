<?php
// Home (index Controllers)
$app->get('/', 'HomeController:index');

/**
 * IFTTT Group
 * Routes within this group can be accessed via /ifttt/v1/ROUTE
 */
$app->group('/ifttt/v1', function () use ($app) {
    // Test cases
    $app->get('/status', 'TestsController:status');
    $app->post('/test/setup', 'TestsController:setup');

    // Air Quality
    $app->post('/triggers/air_quality', 'AirqualityController:index');

    // EMA
    $app->post('/triggers/emergency_notifications', 'EmaController:emergency_notifications');

    // Road Alerts
    $app->post('/triggers/road_alerts', 'RoadAlertsController:index');
});