<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PageRangeTransformer implements DataTransformerInterface
{
public function transform($value)
{
// Eğer veritabanından gelen bir değer varsa, bu değeri uygun formata dönüştür
if ($value !== null && is_array($value)) {
return implode('-', $value);
}

return null;
}

public function reverseTransform($value)
{
if ($value !== null) {
$pages = explode('-', $value);

if (count($pages) === 2) {
// İki sayfa numarası varsa, döndür
return [
'first_page' => (int) $pages[0],
'last_page' => (int) $pages[1],
];
} else {
// Hatalı sayfa aralığı
throw new TransformationFailedException('Geçersiz sayfa aralığı formatı');
}
}

return null;
}
}
