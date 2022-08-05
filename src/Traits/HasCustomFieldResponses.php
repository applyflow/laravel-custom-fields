<?php

namespace Givebutter\LaravelCustomFields\Traits;

use Givebutter\LaravelCustomFields\Models\CustomField;
use Givebutter\LaravelCustomFields\Models\CustomFieldResponse;

trait HasCustomFieldResponses
{
    /**
     * Get the custom field responses for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function customFieldResponses()
    {
        return $this->morphMany(CustomFieldResponse::class, 'model');
    }

    /**
     * Save the given custom fields to the model.
     *
     * @param $fields
     */
    public function saveCustomFields($fields)
    {
        foreach ($fields as $key => $value) {
            $customField = CustomField::find((int) $key);

            if (!$customField) {
                continue;
            }

            if ($customField->type == CustomField::TYPE_MULTISELECT) {
                CustomFieldResponse::where(
                    [
                        'model_id' => $this->id,
                        'field_id' => $customField->id,
                        'model_type' => $this->getMorphClass(),
                    ]
                )->delete();

                if (is_array($value)) {
                    foreach ($value as $ele) {
                        CustomFieldResponse::create([
                            'value' => $ele,
                            'model_id' => $this->id,
                            'field_id' => $customField->id,
                            'model_type' => $this->getMorphClass(),
                        ]);
                    }
                }
            } else {
                CustomFieldResponse::updateOrCreate([
                    'model_id' => $this->id,
                    'field_id' => $customField->id,
                    'model_type' => $this->getMorphClass(),
                ], ['value' => $value,]);
            }
        }
    }

    /**
     * Add a scope to return only models which match the given field and value.
     *
     * @param $query
     * @param CustomField $field
     * @param $value
     */
    public function scopeWhereField($query, CustomField $field, $value)
    {
        $query->whereHas('customFieldResponses', function ($query) use ($field, $value) {
            $query
                ->where('field_id', $field->id)
                ->where(function ($subQuery) use ($value) {
                    $subQuery->hasValue($value);
                });
        });
    }
}
