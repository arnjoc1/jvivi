<?php

namespace tesBundle\outils;

class Saluer
{
    public function Bonjour($user)
    {
        return "Bonjour ".$user;
    }

    public function BonApresMidi($user)
    {
        return "Bon après-midi ".$user;
    }

    public function Bonsoir($user)
    {
        return "Bonsoir ".$user;
    }
}