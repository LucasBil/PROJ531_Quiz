<?php

namespace App\Config;

enum Type: string
{
    case Duo = 'duo';
    case Square = 'carré';
    case Cash = 'cash';
}