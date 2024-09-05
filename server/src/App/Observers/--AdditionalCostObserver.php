<?php
declare(strict_types=1);

namespace Loxya\Observers;

use Loxya\Models\AdditionalCost;

final class AdditionalCostObserver
{
    public $afterCommit = true;

}
