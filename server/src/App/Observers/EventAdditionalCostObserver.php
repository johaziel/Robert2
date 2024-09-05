<?php
declare(strict_types=1);

namespace Loxya\Observers;

use Illuminate\Database\Eloquent\Builder;
use Loxya\Models\Event;
use Loxya\Models\EventAdditionalCost;

final class EventAdditionalCostObserver
{
    public $afterCommit = true;

    public function created(EventAdditionalCost $eventAdditionalCost): void
    {
        $this->syncCache($eventAdditionalCost);
    }

    public function updated(EventAdditionalCost $eventAdditionalCost): void
    {
        $this->syncCache($eventAdditionalCost);
    }

    public function deleted(EventAdditionalCost $eventAdditionalCost): void
    {
        $this->syncCache($eventAdditionalCost);
    }

    // ------------------------------------------------------
    // -
    // -    Event sub-processing
    // -
    // ------------------------------------------------------

    private function syncCache(EventAdditionalCost $eventAdditionalCost): void
    {
        $event = $eventAdditionalCost->event;

        // - Edge case: L'event n'est pas complet => On invalide tout le cache des bookables.
        if (!$event) {
            // phpcs:ignore Generic.Files.LineLength
            debug("[Event] Le matériel d'un événement a été modifié mais il n'a pas été possible de récupérer les modèles liés.");
            debug($eventAdditionalCost->getAttributes());
            container('cache')->invalidateTags([
                Event::getModelCacheKey(),
            ]);
            return;
        }
    }
}
