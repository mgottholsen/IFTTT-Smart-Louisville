<?php

namespace App\Controllers;

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