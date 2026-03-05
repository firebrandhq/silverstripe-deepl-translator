<?php

namespace Arillo\Deepl;

use Amp\Future;
use Amp\Parallel\Worker;
use SilverStripe\Model\List\ArrayList;

/**
 * Runs multiple Deepl API requests in parallel.
 * CAUTION: Can only be used via CLI.
 */
class ParallelTranslator
{
    public static function run(ArrayList $translationDataObjects, $to, $from): ArrayList
    {
        $objects = $translationDataObjects->toArray();

        $executions = [];
        foreach ($objects as $item) {
            $executions[] = Worker\submit(new DeeplTranslateTask($item->getField('Texts'), $to, $from));
        }

        $result = Future\await(array_map(
            fn(Worker\Execution $e) => $e->getFuture(),
            $executions,
        ));

        for ($i = 0; $i < count($result); $i++) {
            $objects[$i]->setField('Results', $result[$i]);
        }

        return new ArrayList($objects);
    }
}