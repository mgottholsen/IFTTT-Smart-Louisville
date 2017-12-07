<?php

namespace Src\Controllers;

use Slim\Views\Twig as View;

class TestsController extends Controller
{
    public function status( $request, $response )
    {
        return $response->withStatus(200);
    }

    public function setup( $request, $response )
    {
        $data = [
            'data' => [
                'samples' => [
                    'triggers' => [
                        'junk_pickup' => [
                            'what_is_your_address' => '121 Freeman AVE'
                        ],
                        'trash_recycling_pickup' => [
                            'what_is_your_address' => '121 Freeman AVE'
                        ],
                        'favorite_restaurant_inspections' => [
                            'restaurant_location' => [
                                "lat" => "38.198825",
                                "lng" => "-85.783031",
                                "address" => "",
                                "description" => ""
                            ]
                        ]
                    ],
                    'triggerFieldValidations' => [
                        'favorite_restaurant_inspections' => [
                            'restaurant_location' => [
                                "valid" => [
                                    "lat" => 38.198825,
                                    "lng" => -85.783031,
                                    "address" => "",
                                    "description" => ""
                                ],
                                "invalid" => [
                                    "lat" => "hello",
                                    "lng" => "no longitude here",
                                    "address" => "",
                                    "description" => ""
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->write(json_encode($data));
    }
}