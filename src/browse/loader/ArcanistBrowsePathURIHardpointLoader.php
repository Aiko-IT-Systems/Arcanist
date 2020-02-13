<?php

final class ArcanistBrowsePathURIHardpointLoader
  extends ArcanistBrowseURIHardpointLoader {

  const LOADERKEY = 'browse.uri.path';
  const BROWSETYPE = 'path';

  public function willLoadBrowseURIRefs(array $refs) {
    $refs = $this->getRefsWithSupportedTypes($refs);
    if (!$refs) {
      return;
    }

    $query = $this->getQuery();

    $working_ref = $query->getWorkingCopyRef();
    if (!$working_ref) {
      echo pht(
        'NO WORKING COPY: The current directory is not a repository '.
        'working copy, so arguments can not be resolved as paths. Run '.
        'this command inside a working copy to resolve paths.');
      echo "\n";
      return;
    }

    $repository_ref = $query->getRepositoryRef();
    if (!$repository_ref) {
      echo pht(
        'NO REPOSITORY: Unable to determine which repository this working '.
        'copy belongs to, so arguments can not be resolved as paths. Use '.
        '"%s" to understand how repositories are resolved.',
        'arc which');
      echo "\n";
      return;
    }
  }

  public function didFailToLoadBrowseURIRefs(array $refs) {
    $refs = $this->getRefsWithSupportedTypes($refs);
    if (!$refs) {
      return;
    }

    $query = $this->getQuery();

    $working_ref = $query->getWorkingCopyRef();
    if (!$working_ref) {
      return;
    }

    $repository_ref = $query->getRepositoryRef();
    if (!$repository_ref) {
      return;
    }

    echo pht(
      'Use "--types path" to force arguments to be interpreted as paths.');
    echo "\n";
  }


  public function loadHardpoints(array $refs, $hardpoint) {
    $query = $this->getQuery();

    $working_ref = $query->getWorkingCopyRef();
    if (!$working_ref) {
      return array();
    }

    $repository_ref = $query->getRepositoryRef();
    if (!$repository_ref) {
      return array();
    }

    $refs = $this->getRefsWithSupportedTypes($refs);
    $project_root = $working_ref->getRootDirectory();

    $results = array();
    foreach ($refs as $key => $ref) {
      $is_path = $ref->hasType(self::BROWSETYPE);

      $path = $ref->getToken();
      if ($path === null) {
        // If we're explicitly resolving no arguments as a path, treat it
        // as the current working directory.
        if ($is_path) {
          $path = '.';
        } else {
          continue;
        }
      }

      $lines = null;
      $parts = explode(':', $path);
      if (count($parts) > 1) {
        $lines = array_pop($parts);
      }
      $path = implode(':', $parts);

      $full_path = Filesystem::resolvePath($path);

      if (!Filesystem::pathExists($full_path)) {
        if (!$is_path) {
          continue;
        }
      }

      if ($full_path == $project_root) {
        $path = '';
      } else {
        $path = Filesystem::readablePath($full_path, $project_root);
      }

      $params = array(
        'path' => $path,
        'lines' => $lines,
        'branch' => $ref->getBranch(),
      );

      $uri = $repository_ref->newBrowseURI($params);

      $results[$key][] = id(new ArcanistBrowseURIRef())
        ->setURI($uri)
        ->setType(self::BROWSETYPE);
    }

    return $results;
  }


}
