<?php

declare(strict_types=1);

namespace Xslain\Printing\Api\PrintNode\Service;

use Xslain\Printing\Api\PrintNode\Resources\Whoami;
use Xslain\Printing\Api\PrintNode\Util\RequestOptions;

class WhoamiService extends AbstractService
{
    public function check(null|array|RequestOptions $opts = null): Whoami
    {
        return $this->request('get', '/whoami', opts: $opts, expectedResource: Whoami::class);
    }
}
