<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformUser\UserSetting\Setting;

use EzSystems\EzPlatformUser\UserSetting\Setting\Value\DateTimeFormat;

final class DateTimeFormatSerializer
{
    public function deserialize(string $value): ?DateTimeFormat
    {
        $value = json_decode($value, true);

        if (!is_array($value)) {
            return null;
        }

        return new DateTimeFormat(
            $value['date_format'] ?? null,
            $value['time_format'] ?? null
        );
    }

    public function serialize(?DateTimeFormat $value): ?string
    {
        if ($value !== null) {
            return json_encode([
                'date_format' => $value->getDateFormat(),
                'time_format' => $value->getTimeFormat(),
            ]);
        }

        return $value;
    }
}
