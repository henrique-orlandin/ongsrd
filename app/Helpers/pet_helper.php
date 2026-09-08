<?php

if (! function_exists('pet_age_label')) {
    function pet_age_label(int $age, string $unit): string
    {
        if ($unit === 'months') {
            return $age === 1 ? '1 mês' : $age . ' meses';
        }

        return $age === 1 ? '1 ano' : $age . ' anos';
    }
}
