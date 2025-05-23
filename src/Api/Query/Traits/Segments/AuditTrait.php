<?php

declare(strict_types=1);

namespace Evgeek\Moysklad\Api\Query\Traits\Segments;

use Evgeek\Moysklad\Api\Query\Segments\Methods\Nested\AuditSegment;

trait AuditTrait
{
    /**
     * Аудит сущности.
     *
     * <code>
     * $audit = $ms->query()
     *  ->entity()
     *  ->customerorder()
     *  ->byId('fb72fc83-7ef5-11e3-ad1c-002590a28eca')
     *  ->audit()
     *  ->get();
     * </code>
     */
    public function audit(): AuditSegment
    {
        return $this->resolveNamedBuilder(AuditSegment::class);
    }
}
