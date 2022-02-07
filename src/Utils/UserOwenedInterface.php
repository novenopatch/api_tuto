<?php

namespace App\Utils;

use App\Entity\User;

interface UserOwenedInterface
{
    public function getUser(): ?User;
    public function setUser(?User $user): self;


}