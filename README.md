
phpDoreso
=========

A [Doreso API](https://developer.doreso.com) PHP client library

**Requirements:**

 - PHP v5.3.3+
 - PHP cURL extension
 - ffmpeg

**Typical usage:**      
    
      require 'phpDoreso.php';
      $doreso =  new Doreso('my api key');
      $d = $doreso->song_identify_file('./test.mp3'); 
      echo($d);


---------------
