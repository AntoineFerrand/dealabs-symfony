<?php

namespace App\Event;


use App\Entity\Account;

class DealCreatedEvent
{
    public const NAME = 'deal.created';

    protected $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}