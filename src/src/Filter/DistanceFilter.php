<?php

namespace App\Filter;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Cache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

final class DistanceFilter extends AbstractContextAwareFilter
{
    const DISTANCE_VALUE = 10;

    const DISTANCE = 'distance';
    const LAT = 'latitude';
    const LON = 'longitude';

    private $appliedAlready = false;

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {

        $queryBuilder
            ->setCacheMode(Cache::MODE_NORMAL)
            ->setCacheable(true)
            ->setLifetime(300);

        // otherwise filter is applied to order and page as well
        if ($this->appliedAlready && !$this->isPropertyEnabled($property, $resourceClass)) {
            return;
        }

        //make sure latitude and longitude are part of specs
        if (!($this->isPropertyMapped(self::LAT, $resourceClass) && $this->isPropertyMapped(self::LON, $resourceClass))) {
            return;
        }

        $query = $this->requestStack->getCurrentRequest()->query;

        foreach ($this->properties as $prop => $val) {
            $this->properties[$prop] = $query->get($prop, null);
        }

        //distance is optional
        if ($this->properties[self::LAT] != null && $this->properties[self::LON] != null) {
            if ($this->properties[self::DISTANCE] == null)
                $this->properties[self::DISTANCE] = self::DISTANCE_VALUE;
        } else {
            //may be we should raise exception
            return;
        }

        $this->appliedAlready = True;

        $queryBuilder
            ->andWhere(
                'ST_distance_sphere(
                        ST_Point(:lat, :lng),
                        o.location
                       ) < :distance'
            )
            ->setParameter('distance', $this->properties[self::DISTANCE]*1000)
            ->setParameter('lat', $this->properties[self::LAT])
            ->setParameter('lng', $this->properties[self::LON]);
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        $description = [
            self::LAT => [
                'property' => self::LAT,
                'type' => 'string',
                'required' => true,
                'swagger' => [
                    'description' => 'Find locations within given radius',
                    'name' => 'distance_filter',
                    'type' => 'filter',
                ],
            ],
            self::LON => [
                'property' => self::LON,
                'type' => 'string',
                'required' => true,
                'swagger' => [
                    'description' => 'Find locations within given radius',
                    'name' => 'distance_filter',
                    'type' => 'filter',
                ],
            ],
            self::DISTANCE => [
                'property' => self::LAT,
                'type' => 'int',
                'required' => false,
                'swagger' => [
                    'minimum' => 5,
                    'maximum' => 25,
                    'description' => 'Find locations within given radius',
                    'name' => 'distance_filter',
                    'type' => 'filter',
                ],
            ],
        ];

        return $description;
    }
}