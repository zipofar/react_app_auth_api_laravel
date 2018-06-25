<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class CountriesController extends Controller
{
    public function index(Request $request)
    {
        ['pName' => $pName, 'pLimit' => $pLimit] = $request->all();

        if (empty($pName)) {
            return response([], Response::HTTP_OK);
        }

        $client = new Client(['base_uri' => 'http://countryapi.gear.host']);
        $response = $client->request(
            'GET',
            '/v1/Country/getCountries',
            ['query' => [
                'pName' => $pName,
                'pLimit' => $pLimit,
            ]])
            ->getBody()
            ->getContents();

        $arrCountries = json_decode($response, true)['Response'];
        $filteredCountries = array_map(function($item) {
            return $item['Name'];
        }, $arrCountries);

        return response($filteredCountries, Response::HTTP_OK);
    }
}