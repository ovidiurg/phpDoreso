
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

Example JSON returned:

     {"status": 1, 
     "msg": "success", 
     "data": [{"album": "Smash The Control Machine", 
     "md5sum": "525c113fa78bcdd2716422c338da47e5", 
     "name": "Run for Cover", 
     "artist_name": "Otep", 
     "play_offset": 15080}]}


---------------


