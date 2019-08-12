<?php

namespace Innerent\Contact\Traits;

use Innerent\Contact\Entities\Contact;

trait HasContacts
{
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
