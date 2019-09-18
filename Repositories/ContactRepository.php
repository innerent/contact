<?php

namespace Innerent\Contact\Repositories;

use Innerent\Contact\Contracts\Contact as ContactInterface;
use Innerent\Contact\Entities\Address;
use Innerent\Contact\Entities\Contact;
use Innerent\Contact\Entities\Email;
use Innerent\Contact\Entities\Phone;

class ContactRepository implements ContactInterface
{
    protected $contact;

    public function make($contactable, $data)
    {
        $this->contact = $contactable->contacts()->create([
            'name' => $data['name'],
            'type' => $data['type'],
        ]);

        return $this;
    }

    public function update($data)
    {
        $this->contact->update(['name' => $data['name']]);

        return $this;
    }

    public function find($contactable, $id)
    {
        $this->contact = $contactable->contacts()->find($id);

        if (! $this->contact instanceof Contact) {
            abort(404);
        }

        return $this;
    }

    public function email(Email $email)
    {
        $this->contact->emails()->updateOrCreate(
            ['id' => $email->id],
            $email->toArray()
        );

        return $this;
    }

    public function address(Address $address)
    {
        $this->contact->addresses()->updateOrCreate(
            ['id' => $address->id],
            $address->toArray()
        );

        return $this;
    }

    public function phone(Phone $phone)
    {
        $this->contact->phones()->updateOrCreate(
            ['id' => $phone->id],
            $phone->toArray()
        );

        return $this;
    }

    public function get($contactable, $id)
    {
        $this->contact = $contactable->contacts()->find($id);

        if (! $this->contact) {
            abort(404);
        }

        $this->contact = $this->contact->load('emails', 'addresses', 'phones');

        return $this;
    }

    public function delete($contactable, $id)
    {
        $this->get($contactable, $id)->toModel()->delete();

        return true;
    }

    public function sync($data)
    {
        $emails = collect($data['emails'])->where('id', '!=', null);
        $this->contact->emails()->whereNotIn('id', $emails->pluck('id')->toArray())->delete();

        $phones = collect($data['phones'])->where('id', '!=', null);
        $this->contact->phones()->whereNotIn('id', $phones->pluck('id')->toArray())->delete();

        $addresses = collect($data['addresses'])->where('id', '!=', null);
        $this->contact->addresses()->whereNotIn('id', $addresses->pluck('id')->toArray())->delete();

        return $this;
    }

    public function toModel()
    {
        return $this->contact->load('emails', 'phones', 'addresses');
    }

    public function toArray()
    {
        return $this->toModel()->toArray();
    }
}
