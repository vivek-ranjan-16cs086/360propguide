<?php

namespace App\Observers;

use App\Jobs\SyncPropertyCustomLinksJob;
use App\Models\Property;

class PropertyObserver
{
    public function created(Property $property): void
    {
        if ($property->status === 'approved') {
            SyncPropertyCustomLinksJob::dispatch();
        }
    }

    public function updating(Property $property): void
    {
        $linkFieldsChanged = $property->isDirty(['city', 'property_type', 'listing_type', 'status']);
        $wasApproved = $property->getOriginal('status') === 'approved';
        $willBeApproved = $property->status === 'approved';

        if ($linkFieldsChanged && ($wasApproved || $willBeApproved)) {
            SyncPropertyCustomLinksJob::dispatch();
        }
    }

    public function deleted(Property $property): void
    {
        if ($property->status === 'approved') {
            SyncPropertyCustomLinksJob::dispatch();
        }
    }
}
