<?php

namespace Src\Controllers;

use Slim\Views\Twig as View;

class EmaController extends Controller
{
    public function emergency_notifications($request, $response){
        $this->logger->info("Emergency_Notification'/ifttt/v1/triggers/emergency_notifications' route - success");
        $error_msgs = array();

        $request_data = json_decode($request->getBody()->getContents(), true);
        if( ! isset( $request_data['triggerFields'] ) ) { $error_msgs[] = array('message' => 'TriggerFields is not set'); }

        $limit = isset( $request_data['limit'] ) && ! empty($request_data['limit']) ? $request_data['limit'] : ( isset( $request_data['limit'] ) && $request_data['limit'] === 0 ? 0 : null );

        if( empty($error_msgs) )
        {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $this->container['settings']['ifttt_vault']['getRave'] );

            if( $res->getStatusCode() == 200 )
            {
                $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' - success");
                $body = $res->getBody()->getContents();
                $reader = new \Sabre\Xml\Reader();
                $reader->xml($body);
                $results = $reader->parse();
                $data = $results['value'];

                $rdata = array(
                    //'created_at' => $data[2]['value'],
                    'created_at' => datetimeformat(null, null,'c'),
                    'sent' => $data[2]['value'],
                    'status' => $data[3]['value'],
                    'event' => $data[3]['value'],
                    'urgency' => $data[6]['value'][3]['value'],
                    'severity' => $data[6]['value'][4]['value'],
                    'certainty' => $data[6]['value'][5]['value'],
                    'effective' => $data[6]['value'][6]['value'],
                    'expires' => $data[6]['value'][8]['value'],
                    'sender' => $data[6]['value'][9]['value'],
                    'headline' => $data[6]['value'][10]['value'],
                    //'description' => $data[7]['value'][11]['value'],
                    'description' => preg_replace('/ {2,}/', ' ', trim($data[6]['value'][11]['value'])),
                    //'date_created' => $data[2]['value']
                    'date_created' => datetimeformat(null, null,'c')
                );


                if( ! empty( $rdata ) )
                {
                    //first check to see if we need to insert a new entry
                    $endata = $this->db->table('emergency_notifications')
                        ->orderBy('date_created', 'desc')
                        ->limit(1)
                        ->get();


                    if(  empty( $endata) || $endata[0]->sent != $rdata['sent'] ) {
                        //insert NEW RECORD!
                        $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' Inserted new Emergency Notification - success");
                        $this->db->table('emergency_notifications')->insertGetId($rdata);

                    }else{
                        $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' Emergency Notification not changed - skipping DB insert");
                    }
                    //get air qulity's
                    $records = $this->db->table('emergency_notifications')
                        ->orderBy('date_created', 'desc')
                        ->limit($limit)
                        ->get();

                    $newarr['data'] = array();

                    foreach( $records as $record )
                    {
                        $newarr['data'][] = array(
                            'sent' => $record->sent,
                            'status' => $record->status,
                            'event' => $record->event,
                            'urgency' => $record->urgency,
                            'severity' => $record->severity,
                            'certainty' => $record->certainty,
                            'effective' => $record->effective,
                            'expires' => $record->expires,
                            'sender' => $record->sender,
                            'headline' => $record->headline,
                            'description' => $record->description,
                            'created_at' => $record->created_at,
                            'meta' => array(
                                'id' => $record->id,
                                'timestamp' => strtotime($record->date_created)
                            )
                        );
                    }

                    $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' API request - success");
                    return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json; charset=utf-8')
                        ->write(json_encode($newarr));
                } else {
                    $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' Properties need to be set - fail");
                    $error_msgs[] = array('status'=> 'SKIP', 'message' => 'Properties need to be set');
                }
            } else {
                $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' Response is empty - fail");
                $error_msgs[] = array('status'=> 'SKIP', 'message' => 'Emergency Notification API pull failed');
            }
        }
        $error = array('errors' => $error_msgs);
        $this->logger->info("Emergency_Notification '/ifttt/v1/triggers/emergency_notifications' errors - fail");
        return $response->withStatus(400)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->write(json_encode($error));

    }
}
