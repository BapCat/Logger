<?php namespace BapCat\Logger;

use BapCat\Facade\Facade;
use BapCat\Persist\Directory;
use BapCat\Phi\Phi;

use Exception;

class Logger {
  const EMERG     = 0;
  const EMERGENCY = 0;
  const ALERT     = 1;
  const CRIT      = 2;
  const CRITICAL  = 2;
  const ERR       = 3;
  const ERROR     = 3;
  const WARN      = 4;
  const WARNING   = 4;
  const NOTICE    = 5;
  const INFO      = 6;
  const DEBUG     = 7;
  const DEBUG1    = 8;
  const DEBUG2    = 9;
  const DEBUG3    = 10;
  const DEBUG4    = 11;
  const DEBUG5    = 12;
  const DEBUG6    = 13;
  const DEBUG7    = 14;
  
  private static $skip_classes = [
      Facade::class,
      Logger::class,
      LogStack::class,
      Phi::class,
  ];
  
  private static $skip_functions = [
    'call_user_func',
    'call_user_func_array',
    'include',
    'include_once',
    'require',
    'require_once',
    'trigger_error'
  ];
  
  private $level = self::INFO;
  
  private $console = false;
  private $fp = false;
  private $file;
  private $time;
  private $logs;
  
  public function __construct($level = null, Directory $logs = null) {
    $this->level($level);
    $this->logs = $logs;
  }
  
  public function level($level = null) {
    if($level !== null) {
      $this->level = $level;
    }
    
    return $this->level;
  }
  
  public function toConsole() {
    $this->console = true;
    return $this;
  }
  
  public function toFile($file) {
    $this->updateFile($file);
    return $this;
  }
  
  public function emerg(...$args) {
    $this->log(static::EMERGENCY, ...$args);
  }
  
  public function emergency(...$args) {
    $this->log(static::EMERGENCY, ...$args);
  }
  
  public function alert(...$args) {
    $this->log(static::ALERT, ...$args);
  }
  
  public function crit(...$args) {
    $this->log(static::CRITICAL, ...$args);
  }
  
  public function critcal(...$args) {
    $this->log(static::CRITICAL, ...$args);
  }
  
  public function err(...$args) {
    $this->log(static::ERROR, ...$args);
  }
  
  public function error(...$args)   { 
    $this->log(static::ERROR, ...$args);
  }
  
  public function warn(...$args) {
    $this->log(static::WARNING, ...$args);
  }
  
  public function warning(...$args) { 
    $this->log(static::WARNING, ...$args);
  }
  
  public function notice(...$args) {
    $this->log(static::NOTICE, ...$args);
  }
  
  public function info(...$args) {
    $this->log(static::INFO, ...$args);
  }
  
  public function debug(...$args) {
    $this->log(static::DEBUG, ...$args);
  }
  
  public function debug1(...$args)  {
    $this->log(static::DEBUG1, ...$args);
  }
  
  public function trace(...$args)  {
    $this->log(static::DEBUG1, ...$args);
  }
  
  public function debug2(...$args)  {
    $this->log(static::DEBUG2, ...$args);
  }
  
  public function debug3(...$args)  { 
    $this->log(static::DEBUG3, ...$args);
  }
  
  public function debug4(...$args)  { 
    $this->log(static::DEBUG4, ...$args);
  }
  
  public function debug5(...$args)  { 
    $this->log(static::DEBUG5, ...$args);
  }
  
  public function debug6(...$args)  { 
    $this->log(static::DEBUG6, ...$args);
  }
  
  public function debug7(...$args)  { 
    $this->log(static::DEBUG7, ...$args);
  }
  
  public function log($level, ...$args) {
    if($this->level < $level) {
      return;
    }
    
    if(empty($this->_file) && ! $this->console) {
      return;
    }
    
    $string = $this->createLogString($level, $args);
    
    if(!empty($this->file)) {
      if($this->time != date('Ymd') || $this->fp === false) {
        $this->initFile();
      }
      
      fwrite($this->fp, $string);
    }
    
    if($this->console) {
      echo $string;
    }
  }
  
  private function updateFile($file) {
    $this->closeFile();
    
    if($this->logs !== null) {
      $file = $this->logs->child[$file]->full_path;
    }
    
    $this->file = $file;
    $this->time = date('Ymd');
  }
  
  private function initFile() {
    $this->updateFile($this->file);
    
    $file = "{$this->file}.{$this->time}";
    $link = "{$this->file}.today";
    $basename = basename($file);
    
    if(!is_link($link) || readlink($link) !== $basename) {
      // We must use ln in overwrite mode (with -f) to get an atomic operation - PHP equivalents don't work.
      exec('ln -fs ' . escapeshellarg($basename) . ' ' . escapeshellarg($link));
    }
    
    // Force chmod 0660 (user/group read/writable only)
    if(!file_exists($file) ) {
      touch($file);
      chmod($file, 0660);
    }
    
    $this->fp = fopen($file, 'a+');
    
    return $this->fp !== false;
  }
  
  private function closeFile() {
    if($this->fp !== false) {
      fflush($this->fp);
      fclose($this->fp);
      $this->fp = false;
    }
  }
  
  private function createLogString($level, array $args) {
    $str = $this->convertArgsToString($args, $level);
    
    $lines = explode("\n", $str);
    
    if(!count($lines)) {
      return '';
    }
    
    $prefix = $this->generateLinePrefix($level);
    
    $toImplode = [];
    
    foreach($lines as $line) {
      if(strlen(trim($line)) === 0) {
        continue;
      }
      
      $toImplode[] = "{$prefix}{$line}\n";
    }
    
    if(count($toImplode) !== 0) {
      return implode($toImplode);
    }
    
    return '';
  }
  
  private function convertArgsToString(array $args, $level) {
    $str = '';
    
    foreach($args as $arg) {
      if(is_int($arg) || is_float($arg) || is_numeric($arg)) {
        $str .= "[$arg]"; 
      } elseif(is_string($arg)) {
        $str .= $arg;
      } elseif(is_null($arg)) {
        $str .= '[NULL]';
      } elseif(is_bool($arg)) {
        $str .= $arg ? '[TRUE]' : '[FALSE]';
      } elseif(is_array($arg)) {
        $str .= '[' . var_export($arg, true) . ']'; 
      } elseif($arg instanceof CustomLogString) {
        $str .= "[{$arg->toLogString($level)}]";
      } elseif($arg instanceof Exception) {
        $str .= "Exception in {$arg->getFile()}({$arg->getLine()}): {$arg->getMessage()}\n{$arg->getTraceAsString()}";
      } else {
        $str .= strval($arg);
      }
    }
    
    return $str;
  }
  
  private function generateLinePrefix() {
    $prefix = '';
    $caller = null;
    
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    
    foreach($trace as $frame) {
      if(
        // Skip classes which generate errors/logging...
        !(array_key_exists('class', $frame) && in_array($frame['class'], static::$skip_classes)) &&
        
        // Skip functions (NOT methods) which generate errors/logging...
        !(!array_key_exists('class', $frame) && array_key_exists('function', $frame) && in_array($frame['function'], static::$skip_functions))
      ) {
        $caller = $frame;
        break;
      }
    }
    
    if(isset($caller['object']) && $caller['object'] instanceof CustomLogPrefix) {
      $prefix = $caller['object']->toLogPrefix($this->level) . '::';
    } elseif(isset($caller['class'])) {
      $prefix = $caller['class'] . '::';
    }
    
    if(isset($caller['function'])) {
      $prefix .= $caller['function'] . '():';
    }
    
    if(!empty($prefix)) {
      $prefix .= ' ';
    }
    
    $prefix = date('H:i:s') . ' [' . getmypid() . "][$this->level]: $prefix";
    
    return $prefix;
  }
}
