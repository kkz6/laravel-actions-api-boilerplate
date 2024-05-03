<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait EnumHelpers
{
    /**
     * Gets the names of the values for an enum.
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * Gets the names for an enum.
     */
    public static function keys(): array
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * Checks if an enum is the same as the value provided.
     */
    public function is(mixed $value): bool
    {
        return $this->value === $value;
    }

    /**
     * Converts the enum into a collection
     */
    public static function asCollection(): Collection
    {
        return Collection::make(items: static::asArray());
    }

    /**
     * Converts the enum into a format the frontend can display.
     */
    public static function asArray(): array
    {
        return array_map(
            fn ($value) => $value->toArray(),
            static::cases()
        );
    }

    /**
     * Converts the enum into an array.
     */
    public function toArray(): array
    {
        return [
            //if we've got a title map we should use that otherwise generate the title based off of its value
            'label' => method_exists($this, 'getTitle')
                ? $this->getTitle()
                : Str::of($this->value)->headline(),

            'value' => $this->value,
        ];
    }
}
