<?php

namespace Innerent\Contact\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Innerent\Contact\Entities\Address;
use Innerent\Contact\Entities\Contact;
use Innerent\Contact\Entities\Email;
use Innerent\Contact\Entities\Phone;
use Innerent\People\Entities\User;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use DatabaseTransactions;

    private $authUser;
    private $contact;

    public function setUp(): void
    {
        parent::setUp();

        $this->authUser = factory(User::class)->create();

        $this->contact = $this->authUser->contacts()->create(factory(Contact::class)->make()->toArray());

        $this->contact->emails()->create(factory(Email::class)->make()->toArray());
        $this->contact->phones()->create(factory(Phone::class)->make()->toArray());
        $this->contact->addresses()->create(factory(Address::class)->make()->toArray());
    }

    public function testCreateContact()
    {
        $data = [
            'name'      => 'My First Contact',
            'type'      => 'location',
            'addresses' => factory(Address::class, 3)->make()->toArray(),
            'phones'    => factory(Phone::class, 2)->make()->toArray(),
            'emails'    => factory(Email::class, 2)->make()->toArray(),
        ];

        $response = $this->actingAs($this->authUser, 'api')->json('post', env('INNERENT_API_PREFIX', 'v1').'/users/'.$this->authUser->uuid.'/contacts', $data);

        $response->assertStatus(201);
    }

    public function testGetContact()
    {
        $this->actingAs($this->authUser, 'api')->json('get', env('INNERENT_API_PREFIX', 'v1').'/users/'.$this->authUser->uuid.'/contacts/'.$this->contact->id)->assertStatus(200);
    }

    public function testListContacts()
    {
        for ($i = 0; $i < 5; $i++) {
            $contact = $this->authUser->contacts()->create(factory(Contact::class)->make()->toArray());

            $contact->emails()->create(factory(Email::class)->make()->toArray());
            $contact->phones()->create(factory(Phone::class)->make()->toArray());
            $contact->addresses()->create(factory(Address::class)->make()->toArray());
        }

        $this->actingAs($this->authUser, 'api')->json('get', env('INNERENT_API_PREFIX', 'v1').'/users/'.$this->authUser->uuid.'/contacts/')->assertStatus(200);
    }

    public function testDeleteContact()
    {
        $this->actingAs($this->authUser, 'api')->json('delete', env('INNERENT_API_PREFIX', 'v1').'/users/'.$this->authUser->uuid.'/contacts/'.$this->contact->id)->assertStatus(204);

        $this->actingAs($this->authUser, 'api')->json('get', env('INNERENT_API_PREFIX', 'v1').'/users/'.$this->authUser->uuid.'/contacts/'.$this->contact->id)->assertStatus(404);
    }

    public function testUpdateContact()
    {
        $data = $this->contact->load('emails', 'phones', 'addresses')->toArray();

        $data['name'] = 'My Edited Contact';

        unset($data['addresses'][1]);
        $data['addresses'][] = factory(Address::class)->make()->toArray();

        foreach (factory(Email::class)->make()->toArray() as $key => $item) {
            $data['emails'][0][$key] = $item;
        }

        foreach (factory(Phone::class)->make()->toArray() as $key => $item) {
            $data['phones'][0][$key] = $item;
        }

        $response = $this->actingAs($this->authUser, 'api')->json('put', env('INNERENT_API_PREFIX', 'v1').'/users/'.$this->authUser->uuid.'/contacts/'.$this->contact->id, $data);

        $response->assertStatus(200);
    }
}
