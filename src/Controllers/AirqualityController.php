<?php

namespace Src\Controllers;

use Slim\Views\Twig as View;

class AirqualityController extends Controller
{
    public function __construct($container)
    {
        parent::__construct($container);
        $this->container=$container;
    }

    public function index( $request, $response )
    {
        $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' route - success");
        $error_msgs = array();

        $request_data = json_decode($request->getBody()->getContents(), true);

        if( ! isset( $request_data['triggerFields'] ) ) { $error_msgs[] = array('message' => 'TriggerFields is not set'); }

        $limit = isset( $request_data['limit'] ) && ! empty($request_data['limit']) ? $request_data['limit'] : ( isset( $request_data['limit'] ) && $request_data['limit'] === 0 ? 0 : null );

        if( empty($error_msgs) )
        {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $this->container['settings']['ifttt_vault']['airQualityURL'] );

            if( $res->getStatusCode() == 200 )
            {
                $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' www.airnowapi.org pull - success");

                $body = $res->getBody()->getContents();
                $reqdata = json_decode($body, true);

                if( ! empty( $reqdata ) ) {

                    $air_quality = $reqdata[0]['AQI'];
                    //$air_quality = rand(1, 500); //run for demo
                    $aql = 'N/A';

                    switch($air_quality)
                    {
                        case ( $air_quality <= 50 ):
                            $aql = 'Good';
                            break;
                        case ( $air_quality <= 100 ):
                            $aql = 'Moderate';
                            break;
                        case ( $air_quality <= 150 ):
                            $aql = 'Unhealthy for Sensitive Groups';
                            break;
                        case ( $air_quality <= 200 ):
                            $aql = 'Unhealthy';
                            break;
                        case ( $air_quality <= 300 ):
                            $aql = 'Very Unhealthy';
                            break;
                        case ( $air_quality >= 300 ):
                            $aql = 'Hazardous';
                            break;
                    }

                    //first check to see if we need to insert a new entry
                    $aqr = $this->db->table('air_quality_record')
                        ->orderBy('date_created', 'desc')
                        ->limit(1)
                        ->get();


                    if( $aqr[0]->index_value != $air_quality ) {
                        //insert NEW RECORD!
                        $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' Inserted new Air quality index - success");
                        $this->db->table('air_quality_record')->insertGetId(array(
                            'index_value' => $air_quality,
                            'label' => $aql,
                            'date_created' => date('Y-m-d H:i:s')
                        ));
                    }else{
                        $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' Air Quality Index not changed - skipping DB insert");
                    }



                    //get air qulity's
                    $records = $this->db->table('air_quality_record')
                        ->orderBy('date_created', 'desc')
                        ->limit($limit)
                        ->get();

                    $newarr['data'] = array();

                    foreach( $records as $record )
                    {
                        $time = datetimeformat(false, false, 'c');

                        switch(rand(1, 100))
                        {
                            case ( $air_quality <= 50 ):
                                $color = 'Green';
                                break;
                            case ( $air_quality <= 100 ):
                                $color = 'Yellow';
                                break;
                            case ( $air_quality <= 150 ):
                                $color = 'Orange';
                                break;
                            case ( $air_quality <= 200 ):
                                $color = 'Red';
                                break;
                            case ( $air_quality <= 300 ):
                                $color = 'Purple';
                                break;
                            case ( $air_quality >= 300 ):
                                $color = 'Maroon';
                                break;
                        }

                        $newarr['data'][] = array(
                            'id' => $record->id,
                            'air_quality_level' => $record->index_value,
                            'air_quality_label' => $record->label,
                            'air_quality_color' => $color,
                            'created_at' => $time,
                            'meta' => array(
                                'id' => $record->id,
                                'timestamp' => strtotime($record->date_created)
                            )
                        );
                    }
                    $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' API request - success");
                    return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json; charset=utf-8')
                        ->write(json_encode($newarr));
                } else {
                    $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' Properties need to be set - fail");
                    $error_msgs[] = array('status'=> 'SKIP', 'message' => 'Properties need to be set');
                }
            } else {
                $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' Response is empty - fail");
                $error_msgs[] = array('status'=> 'SKIP', 'message' => 'Air quality (APCD) API pull failed');
            }
        }
        $error = array('errors' => $error_msgs);
        $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' errors - fail");
        return $response->withStatus(400)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->write(json_encode($error));
    }
}