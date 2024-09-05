<?php
declare(strict_types=1);

namespace Loxya\Models;

use Adbar\Dot as DotArray;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Loxya\Contracts\Serializable;
use Loxya\Errors\Exception\ValidationException;
use Loxya\Models\Traits\Serializer;
use Loxya\Models\Traits\SoftDeletable;
use Loxya\Support\Assert;
use Respect\Validation\Validator as V;

/**
 * Cout addtitionel.
 *
 * @property-read ?int $id
 * @property-read string $name
 * @property-read string|null $description
 * @property float|null $cost_price
 * @property bool $is_hidden_on_bill
 * @property bool $is_discountable
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable|null $updated_at
 *
 * @property-read Collection<array-key, EventAdditionalCost> $assignments
 *
 * @method static Builder|static search(string $term)
 */
final class AdditionalCost extends BaseModel implements Serializable
{
    use Serializer;
    use SoftDeletable;

    /** L'identifiant unique du modèle. */
    public const TYPE = 'additional_costs';

    protected $table = 'additional_costs';

    public const SERIALIZE_FOR_ADDITIONAL_COST = 'additional_costs';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->validation = [
            'additional_id' => V::custom([$this, 'checkAdditionalCostId']),
            'name' => V::optional(V::length(null, 30)),
        ];
    }

    // ------------------------------------------------------
    // -
    // -    Validation
    // -
    // ------------------------------------------------------

    public function checkAdditionalCostId($value)
    {
        // - L'identifiant du cout additional n'est pas encore défini, on skip.
        if (!$this->exists && $value === null) {
            return true;
        }
        return false;
    }

    // ------------------------------------------------------
    // -
    // -    Relations
    // -
    // ------------------------------------------------------



    // ------------------------------------------------------
    // -
    // -    Mutators
    // -
    // ------------------------------------------------------

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
    ];

    // ------------------------------------------------------
    // -
    // -    Setters
    // -
    // ------------------------------------------------------

    protected $fillable = ['name', 'description', 'event_id'];

    // ------------------------------------------------------
    // -
    // -    Query Scopes
    // -
    // ------------------------------------------------------

    protected $orderable = [
        'name',
    ];

    // ------------------------------------------------------
    // -
    // -    Serialization
    // -
    // ------------------------------------------------------

    public function serialize(): array
    {
        return (new DotArray($this->attributesForSerialization()))
            ->delete(['created_at', 'updated_at'])
            ->all();
    }

}