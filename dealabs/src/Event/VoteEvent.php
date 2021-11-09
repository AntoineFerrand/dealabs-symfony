<?php

namespace App\Event;


use App\Entity\Account;

class VoteEvent
{
    public const NAME = 'vote.voted';

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