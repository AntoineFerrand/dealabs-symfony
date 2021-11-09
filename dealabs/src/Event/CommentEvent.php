<?php

namespace App\Event;


use App\Entity\Account;

class CommentEvent
{
    public const NAME = 'comment.commented';

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