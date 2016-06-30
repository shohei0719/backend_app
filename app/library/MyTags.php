<?php

use Phalcon\Tag;

class MyTags extends Tag
{
  static public function errorMsg($message)
  {
    return '<div class="alert alert-danger">' . $message . '</div>';
  }
}
change
