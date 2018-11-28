<?php

namespace App\Http\Controllers;

use Exception;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LineLoginController extends Controller
{
    /**
        * channel id
        * @var string
        */
    private $clientId = 'your channel id';
    /**
        * channel secret
        * @var string
        */
    private $clientSecret = 'your channel secret';
    /**
        * response 可再收到相同的值，可拿來做驗證
        * @var string
        */
    private $state = 'wegames';
    /**
        * openid 必填，profile 可多取姓名、大頭貼 url
        * @var string
        */
    private $scope = 'openid';

    /**
        * 轉址到 Line 登入頁
        *
        * @return \Illuminate\Http\RedirectResponse
        */
    public function index()
    {
        $data = [
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => 'http://{your domain}/api/lineLogin/callback',
            'state'         => $this->state,
            'scope'         => $this->scope
        ];

        $url = 'https://access.line.me/oauth2/v2.1/authorize?' . http_build_query($data);
        return response()->redirectTo($url);
    }

    /**
        * 使用者登入 Line 後，推送資料給我方
        *
        * @param Request $request
        * @return \Illuminate\Http\JsonResponse
        */
    public function callback(Request $request)
    {
        $code = $request->input('code', '');
        $state = $request->input('state', '');

        if (empty($code) || empty($state)) {
            return response()->json('parameter empty', 200);
        }

        if ($state !== $this->state) {
            return response()->json('state error', 200);
        }

        $url = 'https://api.line.me/oauth2/v2.1/token';
        $data = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => url('api/lineLogin/callback'),
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret
            ]
        ];

        try {
            $client = new Client();
            $response = json_decode(
                $client->post($url, $data)->getBody()->getContents()
            );

            $decoded = JWT::decode($response->id_token, $this->clientSecret, ['HS256']);

            $iss = $decoded->iss;
            // user id
            $sub = $decoded->sub;
            // channel id
            $aud = $decoded->aud;
            $exp = $decoded->exp;
            $iat = $decoded->iat;

            if(strpos($this->scope, "profile")) {
                // 姓名
                $name = $decoded->name;
                // 大頭貼url
                $picture = $decoded->picture;
            }
        } catch (Exception $exception) {
            return response()->json('login fail', 200);
        }
    }
}
