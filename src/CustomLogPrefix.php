<?php

/**
 * Interface for classes which want to control their caller prefix, `Class::Function` by default
 */
interface CustomLogPrefix {
  /**
   * Translate `$this` into a string suitable for logging
   * 
   * @param  int  $level
   * 
   * @return  string
   */
  public function toLogPrefix($level);
}
