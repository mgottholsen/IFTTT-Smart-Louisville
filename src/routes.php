<?php
// Home (index Controllers)
$app->get('/', 'HomeController:index');

// Test cases
$app->get('/ifttt/v1/status', 'TestsController:status');
$app->post('/ifttt/v1/test/setup', 'TestsController:setup');

// Air Quality
$app->post('/ifttt/v1/triggers/air_quality', 'AirqualityController:index');

// EMA
$app->post('/ifttt/v1/triggers/emergency_notifications', 'EmaController:emergency_notifications');

// Road Alerts
$app->post('/ifttt/v1/triggers/road_alerts', 'RoadAlertsController:index');