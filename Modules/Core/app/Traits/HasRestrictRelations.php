<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Config;
use Modules\Core\Helpers\Helper;

trait HasRestrictRelations
{
    public static function pushRelation(string $relName, bool $restricted = true): void
    {
        if (app()->configurationIsCached()) {
            return;
        }

        Config::push(self::generateConfigName(), [
            'title' => $relName,
            'restricted' => $restricted,
        ]);
    }

    public static function generateConfigName(): string
    {
        return Helper::getModuleFromClass(
            static::class
        ).'.relations.'.basename(static::class);
    }

    public function hasRestrictedRelations(): bool
    {
        $rels = static::getRestrictedRelations();

        $query = static::query()->where('id', $this->id)
            ->where(function ($q) use ($rels) {
                foreach ($rels as $key => $rel) {
                    $key === 0
                        ? $q->has($rel)
                        : $q->orHas($rel);
                }
            });

        return $rels && $query->first();

    }

    public static function getRestrictedRelations(): ?array
    {
        $res = [];

        foreach (self::getAllRelations() as $rel) {
            if ($rel['restricted']) {
                $res[] = $rel['title'];
            }
        }

        array_push($res, ...static::internalRestrictedRelations());

        return $res;
    }

    public static function getAllRelations(): ?array
    {
        return \config(self::generateConfigName()) ?? [];
    }

    public static function internalRestrictedRelations(): array
    {
        return [];
    }

    public function restrictedErrMsg(): string
    {
        return __('Core::validations.restricted_relation');
    }
}
