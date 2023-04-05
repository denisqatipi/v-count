<?php

namespace Tests\Http\SaloonRequests;

use App\Http\SaloonRequests\GetVCountDataRequest;
use PHPUnit\Framework\TestCase;

class GetVCountDataRequestTest extends TestCase
{
    public function testRequest()
    {
        $response = (new GetVCountDataRequest())->send();

        dd($response->json());

    }

}
