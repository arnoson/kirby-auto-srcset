<?php

use Kirby\Toolkit\A;

return [
  'autoSrcset' =>
    /**
     * Create a srcset definition for the min and max width and automatically
     * generate the widths in between.
     * Based on: https://processwire.com/talk/topic/12036-responsive-image-breakpoints-with-field-templates/
     */
    function (array $options = []): ?string {
      $options = A::merge(
        option('arnoson.kirby-auto-srcset'),
        array_filter($options)
      );

      $minWidth = $options['minWidth'];
      $maxWidth = $options['maxWidth'];
      $maxSteps = $options['maxSteps'];
      $thumb = $options['thumb'];
      $ratio = $options['ratio'] ?? null;

      // Calculate the cropped dimensions if a custom ratio is specified.
      if ($ratio) {
        if ($ratio < 1) {
          $width = min($this->height() * $ratio, $this->width());
          $height = $width / $ratio;
        } else {
          $height = min($this->width() / $ratio, $this->height());
          $width = $height * $ratio;
        }
      } else {
        $width = $this->width();
        $height = $this->height();
        $ratio = $this->ratio();
      }

      // The option is specified in kb, but we calculate with bytes.
      $fileSizeStep = $options['fileSizeStep'] * 1024;

      // Make sure that the images doesn't get blown up.
      $maxWidth = min($width, $maxWidth);

      $minFile = $this->thumb(
        A::merge($thumb, [
          'width' => round($minWidth),
          'height' => round($minWidth / $ratio),
        ])
      );

      $maxFile = $this->thumb(
        array_merge($thumb, [
          'width' => round($maxWidth),
          'height' => round($maxWidth / $ratio),
        ])
      );

      $fileSizeDifference = $maxFile->size() - $minFile->size();

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
        $sizes[$width . 'w'] = array_merge($thumb, [
          'width' => round($width),
          'height' => round($width / $ratio),
        ]);
      }

      return $this->srcset($sizes);
    },
];