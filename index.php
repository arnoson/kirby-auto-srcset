<?php

Kirby::plugin('arnoson/kirby-auto-srcset', [
  'options' => [
    'minWidth' => 300,
    'maxWidth' => 1000,
    'quality' => 80,
    'fileSizeStep' => 20,
    'maxSteps' => 10,
  ],
  'fileMethods' => require_once __DIR__ . '/fileMethods/autoSrcset.php'
]);