<?php

return [
  'autoSrcset' =>
    /**
     * Create a srcset definition for the min and max width and automatically
     * generate the widths in between.
     * Based on: https://processwire.com/talk/topic/12036-responsive-image-breakpoints-with-field-templates/
     */
    function (array $options = []): ?string {
      $options = array_merge(option('arnoson.kirby-auto-srcset'), $options);
      $minWidth = $options['minWidth'];
      $maxWidth = $options['maxWidth'];
      $maxSteps = $options['maxSteps'];

      // The option is specified in kb, but we calculate with bytes.
      $fileSizeStep = $options['fileSizeStep'] * 1024;

      // Make sure tha images doesn't get blown up.
      $maxWidth = min($this->width(), $maxWidth);

      $minFile = $this->thumb(array_merge($options, ['width' => $minWidth]));
      $maxFile = $this->thumb(array_merge($options, ['width' => $maxWidth]));
      $fileSizeDifference = $maxFile->size() - $minFile->size();

      $ratio = $this->width() / $this->height();
      $minArea = round($minWidth * ($minWidth / $ratio));
      $maxArea = round($maxWidth * ($maxWidth / $ratio));
      $areaDifference = $maxArea - $minArea;

      $widths = [$minWidth];
      if ($fileSizeDifference > $fileSizeStep) {
        $steps = min(round($fileSizeDifference / $fileSizeStep), $maxSteps + 1);
        $areaStep = $areaDifference / $steps;
        for ($i = 1; $i < $steps; $i++) {
          $area = $minArea + $areaStep * $i;
          $widths[] = round(sqrt($area * $ratio));
        }
      }
      $widths[] = $maxWidth;

      $sizes = [];
      foreach ($widths as $width) {
        $sizes[$width . 'w'] = array_merge($options, ['width' => $width]);
      }

      return $this->srcset($sizes);
    },
];