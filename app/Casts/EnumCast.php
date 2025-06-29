<?php


namespace App\Casts;

use App\Enum\Permission as PermissionEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class EnumCast implements CastsAttributes
{
    protected string $enumClass;

    public function __construct(string $enumClass)
    {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("The given class {$enumClass} is not an enum.");
        }

        $this->enumClass = $enumClass;
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return $value !== null ? $this->enumClass::from($value) : null;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof $this->enumClass) {
            return $value->value;
        }

        throw new InvalidArgumentException("The given value is not an instance of {$this->enumClass}.");
    }
}
