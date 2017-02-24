<?php

/**
 * Interface for classes which want to control their log message
 */
interface CustomLogString {
  /**
   * Translate `$this` into a string suitable for logging
   *
   * @param  int  $level
   * 
   * @return  string
   */
  public function toLogString($level);
}
