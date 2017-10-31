<?php

namespace App\Controllers;

use Slim\Views\Twig as View;

class AirqualityController extends Controller
{
    public function index( $request, $response )
    {
        $this->logger->info("road_alerts '/ifttt/v1/triggers/road_alerts' route - success");
        $error_msgs = array();

        $request_data = json_decode($request->getBody()->getContents(), true);

        if( ! isset( $request_data['triggerFields'] ) ) { $error_msgs[] = array('message' => 'TriggerFields is not set'); }

        $limit = isset( $request_data['limit'] ) && ! empty($request_data['limit']) ? $request_data['limit'] : ( isset( $request_data['limit'] ) && $request_data['limit'] === 0 ? 0 : null );

        if( empty($error_msgs) )
        {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', 'https://www.waze.com/rtserver/web/TGeoRSS?ma=600&mj=100&mu=100&left=-86.17898941040039&right=-85.16000747680664&bottom=38.01268909779125&top=38.40331957995864&_=1498481948793');

            if( $res->getStatusCode() == 200 )
            {
                $this->logger->info("road_alerts '/ifttt/v1/triggers/road_alerts' Road Alerts pull - success");

                $body = $res->getBody()->getContents();
                $reqdata = json_decode($body, true);

                if( ! empty( $reqdata ) ) {

                    $road_alerts = $jsondata[0]['type'];
                    $aql = 'N/A';

                    switch($road_alerts)
                    {
                        case ( $road_alerts <= 50 ):
                            $aql = 'Good';
                            break;
                        case ( $road_alerts <= 100 ):
                            $aql = 'Moderate';
                            break;
                        case ( $road_alerts <= 150 ):
                            $aql = 'Unhealthy for Sensitive Groups';
                            break;
                        case ( $road_alerts <= 200 ):
                            $aql = 'Unhealthy';
                            break;
                        case ( $road_alerts <= 300 ):
                            $aql = 'Very Unhealthy';
                            break;
                        case ( $road_alerts >= 300 ):
                            $aql = 'Hazardous';
                            break;
                    }

                    //first check to see if we need to insert a new entry
                    $aqr = $this->db->table('air_quality_record')
                        ->orderBy('date_created', 'desc')
                        ->limit(1)
                        ->get();


                    if( $aqr[0]->index_value != $road_alerts ) {
                        //insert NEW RECORD!
                        $this->logger->info("air_quality '/ifttt/v1/triggers/air_quality' Inserted new Air quality index - success");
                        $this->db->table('air_quality_record')->insertGetId(array(
                            'index_value' => $road_alerts,
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
                            case ( $road_alerts <= 50 ):
                                $color = 'Green';
                                break;
                            case ( $road_alerts <= 100 ):
                                $color = 'Yellow';
                                break;
                            case ( $road_alerts <= 150 ):
                                $color = 'Orange';
                                break;
                            case ( $road_alerts <= 200 ):
                                $color = 'Red';
                                break;
                            case ( $road_alerts <= 300 ):
                                $color = 'Purple';
                                break;
                            case ( $road_alerts >= 300 ):
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
