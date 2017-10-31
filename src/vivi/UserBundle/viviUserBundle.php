<?php

namespace vivi\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class viviUserBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}