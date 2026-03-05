<?php

namespace Arillo\Deepl;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;

class DeeplTranslateTask implements Task
{
    public function __construct(
        private readonly mixed $texts,
        private readonly string $to,
        private readonly ?string $from,
    ) {}

    public function run(Channel $channel, Cancellation $cancellation): mixed
    {
        return Deepl::translate($this->texts, $this->to, $this->from);
    }
}