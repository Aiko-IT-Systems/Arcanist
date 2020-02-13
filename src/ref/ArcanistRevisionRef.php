<?php

final class ArcanistRevisionRef
  extends ArcanistRef {

  private $parameters;
  private $sources = array();

  public function getRefIdentifier() {
    return pht('Revision %s', $this->getMonogram());
  }

  public function defineHardpoints() {
    return array();
  }

  public static function newFromConduit(array $dict) {
    $ref = new self();
    $ref->parameters = $dict;
    return $ref;
  }

  public function getMonogram() {
    return 'D'.$this->getID();
  }

  public function getStatusDisplayName() {
    return idx($this->parameters, 'statusName');
  }

  public function isClosed() {
    // TODO: This should use sensible constants, not English language
    // display text.
    switch ($this->getStatusDisplayName()) {
      case 'Abandoned':
      case 'Closed':
        return true;
    }

    return false;
  }

  public function getURI() {
    return idx($this->parameters, 'uri');
  }

  public function getFullName() {
    return pht('%s: %s', $this->getMonogram(), $this->getName());
  }

  public function getID() {
    return idx($this->parameters, 'id');
  }

  public function getName() {
    return idx($this->parameters, 'title');
  }

  public function getAuthorPHID() {
    return idx($this->parameters, 'authorPHID');
  }

  public function addSource(ArcanistRevisionRefSource $source) {
    $this->sources[] = $source;
    return $this;
  }

  public function getSources() {
    return $this->sources;
  }

}
