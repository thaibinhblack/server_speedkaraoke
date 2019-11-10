<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sid = 'ACcd6bb7cabf87808423aa180a9e1acc49';
        $token = 'b4cfc1ed2a215abc88db477577001447';
        $client = new Client($sid, $token);
        $result  = $client->messages->create(
            // the number you'd like to send the message to
            '+84825468971',
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+84825468971',
                // the body of the text message you'd like to send
                'body' => 'Hey Jenny! Good luck on the bar exam!'
            )
        );
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post("https://api.speedsms.vn/index.php/sms/send?access-token=MfP_ttePiw-tGn7VQ2FHPssrdKpLxwkc",[
            "to" => "84825468971",
            "content" => "Hello bạn Bình",
            "type" => 2
        ]);

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
