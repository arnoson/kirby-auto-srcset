<?php

return [
  'autoSrcset' => 
  /**
   * Create a srcset definition for the min and max width and automatically
   * generate the widths in between.
   * Based on: https://processwire.com/talk/topic/12036-responsive-image-breakpoints-with-field-templates/
   *
   * @param int $minWidth
   * @param int $maxWidth
   * @param int $quality
   * @param int $fileSizeStep
   * @return string\null
   */
  function (
    int $minWidth = null,
    int $maxWidth = null,
    int $quality = null,
    int $fileSizeStep = null,
    int $maxSteps = null
  ): ?string {
    $minWidth = $minWidth ?? option('arnoson.auto-srcset.minWidth', 300);
    $maxWidth = $maxWidth ?? option('arnoson.auto-srcset.maxWidth', 1024);
    $quality = $quality ?? option('arnoson.auto-srcset.quality');
    $maxSteps = $maxSteps ?? option('arnoson.auto-srcset.maxSteps', 10);
    $fileSizeStep = $fileSizeStep ?? option('arnoson.auto-srcset.fileSizeStep');
    // The argument is specified in kb, but we calculate with bytes.
    $fileSizeStep *= 1024;
  
    $minFile = $this->resize($minWidth, null, $quality);
    $maxFile = $this->resize($maxWidth, null, $quality);
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
      $sizes[$width . 'w'] = ['width' => $width, 'quality' => $quality];
    }
  
    return $this->srcset($sizes);
  }
];