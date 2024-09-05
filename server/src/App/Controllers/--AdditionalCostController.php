<?php
declare(strict_types=1);

namespace Loxya\Controllers;

use DI\Container;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Illuminate\Database\Eloquent\Builder;
use Loxya\Controllers\Traits\WithCrud;
use Loxya\Http\Request;
use Loxya\Models\Document;
use Loxya\Models\Event;
use Loxya\Models\EventAdditionalCost;
use Loxya\Models\AdditionalCost;
use Loxya\Services\Auth;
use Loxya\Services\I18n;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Http\Response;

final class AdditionalCostController extends BaseController
{
    use WithCrud;

    private I18n $i18n;

    public function __construct(Container $container, I18n $i18n)
    {
        parent::__construct($container);

        $this->i18n = $i18n;
    }
    // ------------------------------------------------------
    // -
    // -    Actions
    // -
    // ------------------------------------------------------

    public function getAll(Request $request, Response $response): ResponseInterface
    {
        $additional_costs = AdditionalCost::orderBy('id', 'asc')->get();

        //$additional_costs = $additional_costs->map(static fn ($additional_cost) => static::_formatOne($additional_cost));
        return $response->withJson($additional_costs, StatusCode::STATUS_OK);

    }

    
    public function getAllWhileEvent(Request $request, Response $response): ResponseInterface
    {
        $eventId = $request->getIntegerAttribute('eventId');
        $event = Event::findOrFail($eventId);

        $additional_costs = AdditionalCost::query()
            ->customOrderBy('name')->get()
            ->map(static function ($additional_cost) use ($event) {
                $events = $additional_cost->assignments()
                    ->whereHas('event', static function (Builder $query) use ($event) {
                        /** @var Builder|Event $query */
                        $query
                            ->inPeriod($event)
                            ->where('id', '!=', $event->id)
                            ->where('deleted_at', null);
                    })
                    ->get()
                    ->map(static fn (EventAdditionalCost $eventAdditionalCost) => (
                        $eventAdditionalCost->serialize(
                            EventAdditionalCost::SERIALIZE_FOR_ADDITIONAL_COST,
                        )
                    ))
                    ->all();

                return array_replace($additional_cost->serialize(), compact('events'));
            })
            ->all();

        return $response->withJson($additional_costs, StatusCode::STATUS_OK);
    }

    // ------------------------------------------------------
    // -
    // -    Méthodes internes
    // -
    // ------------------------------------------------------

    protected static function _formatOne(AdditionalCost $additionalCost): array
    {
        return $additionalCost->serialize();
    }

}