<?php namespace BapCat\Logger;

class LogStack {
  /**
   * @var  Logger[]  $loggers  Our logger stack
   */
  private $loggers = [];
  
  /**
   * @var  ?Logger  $top  The logger on the top of the stack
   */
  private $top = null;
  
  /**
   * Gets the `Logger` on the top of the stack.  If the stack is empty, it pushes
   * a new logger at the default log level that writes to the file "limbo"
   * 
   * @return  Logger
   */
  public function get() {
    if($this->top === null) {
      $this->push()->toFile('limbo');
    }
    
    return $this->top;
  }
  
  /**
   * Push a new logger to the stack
   * 
   * @param  ?int  $level  The minimum log level to write
   * 
   * @return  Logger  The new logger
   */
  public function push($level = null) {
    return $this->loggers[] = $this->top = new Logger($level);
  }
  
  /**
   * Pop a logger off the stack
   * 
   * @return  Logger  The old logger
   */
  public function pop() {
    $popped = array_pop($this->loggers);
    $this->top = end($this->loggers) ?: null;
    return $popped;
  }
  
  /**
   * Close all loggers
   * 
   * @return  void
   */
  public function close() {
    foreach($this->loggers as $logger) {
      $logger->close();
    }
    
    $this->loggers = [];
    $this->top = null;
  }
  
  /**
   * Get or set the current log level
   * 
   * @param  ?int  $level
   * 
   * @return  int  The current log level
   */
  public function level($level = null) {
    return $this->get()->level($level);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function emerg(...$args) {
    $this->get()->log(Logger::EMERGENCY, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function emergency(...$args) {
    $this->get()->log(Logger::EMERGENCY, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function alert(...$args) {
    $this->get()->log(Logger::ALERT, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function crit(...$args) {
    $this->get()->log(Logger::CRITICAL, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function critcal(...$args) {
    $this->get()->log(Logger::CRITICAL, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function err(...$args) {
    $this->get()->log(Logger::ERROR, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function error(...$args)   { 
    $this->get()->log(Logger::ERROR, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function warn(...$args) {
    $this->get()->log(Logger::WARNING, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function warning(...$args) { 
    $this->get()->log(Logger::WARNING, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function notice(...$args) {
    $this->get()->log(Logger::NOTICE, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function info(...$args) {
    $this->get()->log(Logger::INFO, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug(...$args) {
    $this->get()->log(Logger::DEBUG, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug1(...$args)  {
    $this->get()->log(Logger::DEBUG1, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function trace(...$args)  {
    $this->get()->log(Logger::DEBUG1, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug2(...$args)  {
    $this->get()->log(Logger::DEBUG2, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug3(...$args)  { 
    $this->get()->log(Logger::DEBUG3, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug4(...$args)  { 
    $this->get()->log(Logger::DEBUG4, ...$args);
  }
  
  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug5(...$args)  { 
    $this->get()->log(Logger::DEBUG5, ...$args);
  }

  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug6(...$args)  { 
    $this->get()->log(Logger::DEBUG6, ...$args);
  }

  /**
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function debug7(...$args)  { 
    $this->get()->log(Logger::DEBUG7, ...$args);
  }
  
  /**
   * @param  int       $level
   * @param  ...mixed  $args
   * 
   * @return  void
   */
  public function log($level, ...$args) {
    $this->get()->log($level, ...$args);
  }
}
