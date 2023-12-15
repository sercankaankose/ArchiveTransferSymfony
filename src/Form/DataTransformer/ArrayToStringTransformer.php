<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        // Diziyi stringe dönüştür
        if (!is_array($value)) {
            return '';
        }

        return implode(', ', $value);
    }
    

    public function reverseTransform($value): array
    {
        // Stringi diziye dönüştür
        return explode(', ', $value);
    }
}
