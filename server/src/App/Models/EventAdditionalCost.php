<?php
declare(strict_types=1);

namespace Loxya\Models;

use Adbar\Dot as DotArray;
use Carbon\CarbonImmutable;
use Brick\Math\BigDecimal as Decimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Loxya\Contracts\Serializable;
use Loxya\Models\Traits\Serializer;
use Loxya\Models\Traits\TransientAttributes;
use Respect\Validation\Validator as V;

/**
 * Cout additionel utilisé dans un événement.
 *
 * @property-read ?int $id
 * @property int $event_id
 * @property-read Event $event

 * @property-read string $name
 * @property-read string|null $description
 * @property float|null $cost_price
 * @property Decimal $total_costs
 * @property bool $is_hidden_on_bill
 * @property bool $is_discountable
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
final class EventAdditionalCost extends BaseModel implements Serializable
{
    use AsPivot;
    use Serializer;
    use TransientAttributes;

    protected $table = 'event_additional_costs';
    public $timestamps = false;

    // - Types de sérialisation.
    public const SERIALIZE_DEFAULT = 'default';
    public const SERIALIZE_FOR_EVENT = 'default-for-event';
    public const SERIALIZE_FOR_ADDITIONAL_COST = 'default-for-additional-cost';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->validation = [
            'event_id' => V::custom([$this, 'checkEventId']),
            //'quantity' => V::intVal()->min(1),
        ];
    }

    // ------------------------------------------------------
    // -
    // -    Validation
    // -
    // ------------------------------------------------------

    public function checkEventId($value)
    {
        // - L'identifiant de l'événement n'est pas encore défini, on skip.
        if (!$this->exists && $value === null) {
            return true;
        }
        return Event::staticExists($value);
    }

    // ------------------------------------------------------
    // -
    // -    Relations
    // -
    // ------------------------------------------------------

    public function event(){
        return $this->belongsTo(Event::class);
            //->withTrashed();
    }

    /*public function additionalcost()
    {
        return $this->belongsTo(AdditionalCost::class)
            ->withTrashed();
    }*/

    // ------------------------------------------------------
    // -
    // -    Mutators
    // -
    // ------------------------------------------------------

    protected $casts = [
        'event_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        //'is_hidden_on_bill' => 'boolean',
        //'is_discountable' => 'boolean',
        'cost_price' => 'float',
        //'created_at' => 'immutable_datetime',
        //'updated_at' => 'immutable_datetime',
        //'quantity' => 'integer',
    ];

    // ------------------------------------------------------
    // -
    // -    Setters
    // -
    // ------------------------------------------------------

    protected $fillable = ['name', 'description', 'event_id', 'cost_price'];

    public function getNameAttribute($value)
    {
        return $value;
    }

    public function getUnitCostAttribute(): ?Decimal
    {
        if (!$this->event->is_billable) {
            return null;
        }

        // Note: Le fait d'arriver jusqu'ici avec un prix à `null` ne devrait
        //       jamais arriver mais si le `billingMode` est changé dans le
        //       fichier de config. sans passer par l'edition de tous les
        //       matériels c'est ce qui se produira donc...
        return Decimal::of($this->cost_price ?? Decimal::zero())
            ->toScale(2, RoundingMode::UNNECESSARY);
    }

    public function getTotalCostsPriceAttribute(): ?Decimal
    {
        if (!$this->event->is_billable) {
            return null;
        }

        return $this->cost_price;
            //->multipliedBy($this->quantity)
            //->multipliedBy(1);
    }

   // ------------------------------------------------------
    // -
    // -    Méthodes de "repository"
    // -
    // ------------------------------------------------------

    public static function flushForEvent(int $eventId): void
    {
        static::where('event_id', $eventId)->delete();
    }

    // ------------------------------------------------------
    // -
    // -    Serialization
    // -
    // ------------------------------------------------------

    public function serialize(string $format = self::SERIALIZE_DEFAULT): array
    {
        /** @var EventAdditionalCost $eventAdditionalCost */
        $eventAdditionalCost = tap(clone $this, static function (EventAdditionalCost $eventAdditionalCost) use ($format) {

            if ($format === self::SERIALIZE_FOR_ADDITIONAL_COST) {
                $eventAdditionalCost->append('event');
            }
        });

        return (new DotArray($eventAdditionalCost->attributesForSerialization()))
            ->all();
    }

}