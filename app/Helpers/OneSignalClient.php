<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class OneSignalClient
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param string $baseUrl
     * @param string $appId
     * @param string $apiKey
     */
    public function __construct(string $baseUrl, string $appId, string $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->appId = $appId;
        $this->apiKey = $apiKey;
    }

    /**
     * @param \App\Models\Push $push
     */
    public function sendPush(\App\Models\Push $push)
    {
        $data = [
            'headings' => [
                'en' => $push->getTitle()
            ],
            'data' => $push->getParameters(),
            'ios_category' => $push->getCategory()
        ];

        if ($push->getText()) {
            $data['contents'] = [
                'en' => $push->getText()
            ];
        }

        if ($push->getIncreaseBadge()) {
            $data['ios_badgeType'] = 'Increase';
            $data['ios_badgeCount'] = 1;
        }

        if ($push->getOneSignalPlayerId()) {
            $data['include_player_ids'] = [
                $push->getOneSignalPlayerId()
            ];
        }

        if ($push->getSubtitle()) {
            $data['subtitle'] = [
                'en' => $push->getSubtitle()
            ];
        }

        if ($push->getExistsTags()) {
            if (!isset($data['filters'])) {
                $data['filters'] = [];
            }

            foreach ($push->getExistsTags() as $tag) {
                $data['filters'][] = [
                    'field' => 'tag',
                    'key' => $tag,
                    'relation' => 'exists'
                ];
            }
        }

        if ($push->getLatitude() && $push->getLongitude()) {
            if (!isset($data['filters'])) {
                $data['filters'] = [];
            }

            $data['filters'][] = [
                'field' => 'location',
                'radius' => 3000,
                'lat' => $push->getLatitude(),
                'long' => $push->getLongitude()
            ];
        }

        $this->sendRequest('/notifications', $data);
    }

    /**
     * @param string $id
     * @param string $templateId
     */
    public function sendPushByTemplateId(string $id, string $templateId)
    {
        $data = [
            'include_player_ids' => [
                $id
            ],
            'template_id' => $templateId
        ];

        $this->sendRequest('/notifications', $data);
    }

    /**
     * @param string $url
     * @param array $data
     * @return bool
     */
    private function sendRequest(string $url, array $data)
    {
        $data['app_id'] = $this->appId;

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey
        ])->post($this->baseUrl . $url, $data);

        return $response->getStatusCode() === 200;
    }
}
