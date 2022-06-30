<?php

final class PhutilUrisprintfTestCase extends PhutilTestCase {

  public function testUrisprintf() {
    $this->assertEqual(
      'x.com?a=huh%3F',
      urisprintf('x.com?a=%s', 'huh?'));

    $this->assertEqual(
      '/a/origin%252Fmain/z/',
      urisprintf('/a/%p/z/', 'origin/main'));

    $this->assertEqual(
      'y.com?%21&%23',
      vurisprintf('y.com?%s&%s', array('!', '#')));
  }

}
