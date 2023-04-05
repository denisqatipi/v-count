<?php

namespace App\Http\SaloonRequests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class GetVCountDataRequest extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return 'https://cloud.v-count.com/api/v4/vcountapi';
    }

    public function defaultBody(): array
    {
        return [
            'username' => 'zero.albania',
            'password' => '&8u_d+**6(4LGrw',
            'format' => 'json',
            'start_date' => date('Y-m-d'),
            'finish_date' => date('Y-m-d'),
            'store' => 'all',
        ];
    }

}
