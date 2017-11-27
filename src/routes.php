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

    // Favorite Restaurant Inspection
    $app->post('/triggers/favorite_restaurant_inspections', 'RestaurantInspectionController:favorite_restaurant_inspections');
    $app->post('/triggers/favorite_restaurant_inspections/fields/restaurant_address/validate','RestaurantInspectionController:favorite_restaurant_inspections_restaurant_address_validation');
});


/**
 * Cron Group
 * Routes within this group can be accessed via /cron/ROUTE_HERE
 */
$app->group('/cron', function () use ($app) {
    $app->get('/import_restaurant_inspections', 'RestaurantInspectionController:dataImport');
});
