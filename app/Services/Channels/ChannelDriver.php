<?php

namespace App\Services\Channels;

use App\Models\Channel;

interface ChannelDriver
{
    public function pull(Channel $channel): array;
}
